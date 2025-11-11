<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\PredictionCardsMatches;
use App\Models\PredictionCardsMatchScorer;
use Carbon\Carbon;

class ImportOldFinishedMatches extends Command
{
    protected $signature = 'import:old-finished-matches {--truncate : Truncate PredictionCardsMatches and Scorers before importing}';
    protected $description = '⚽ Import finished matches from old database into PredictionCardsMatches and PredictionCardsMatchScorer tables.';

    public function handle(): int
    {
        $this->info('🚀 Starting import from old table [finished_match_results]...');

        $oldConnection = 'old_mysql';

        // Optional truncate
        if ($this->option('truncate')) {
            DB::statement('DELETE FROM prediction_cards_match_scorers');
            DB::statement('DELETE FROM prediction_cards_matches');
            $this->warn('⚠️ Cleared both prediction_cards_matches and prediction_cards_match_scorers tables.');
        }

        $records = DB::connection($oldConnection)->table('finished_match_results')->get();
        $this->info("📦 Found {$records->count()} records to import...");

        $yesterday = Carbon::yesterday()->format('Y-m-d 00:00:00');
        $importedMatches = 0;
        $importedScorers = 0;

        foreach ($records as $old) {
            try {
                // 🏟️ Insert match
                $match = PredictionCardsMatches::create([
                    'league_id'            => $old->league_id,
                    'match_id'             => $old->match_id,
                    'home_team_id'         => $old->home_team_id,
                    'away_team_id'         => $old->away_team_id,
                    'starting_at'          => $yesterday,
                    'home_team_result'     => $old->home_team_result ?? 0,
                    'away_team_result'     => $old->away_team_result ?? 0,
                    'status'               => 1,
                    'prediction_calculate' => 1,
                    'card_calculate'       => 1,
                    'created_at'           => now(),
                    'updated_at'           => now(),
                ]);

                $importedMatches++;

                // ⚽ Parse scorers
                $this->insertScorers($match->id, $old->home_team_scorers ?? null, 'home', $importedScorers);
                $this->insertScorers($match->id, $old->away_team_scorers ?? null, 'away', $importedScorers);

            } catch (\Exception $e) {
                $this->error("❌ Failed to import match ID {$old->match_id}: {$e->getMessage()}");
            }
        }

        $this->info("✅ Imported {$importedMatches} matches and {$importedScorers} scorers successfully.");
        return Command::SUCCESS;
    }

    /**
     * Insert scorers from JSON/array-like string, mapping old api_id to new player.id
     */
    private function insertScorers($predictionMatchId, $rawData, $teamSide, &$counter)
    {
        if (!$rawData) return;

        $ids = $this->extractIds($rawData);
        if (empty($ids)) return;

        foreach ($ids as $apiId) {
            // 🧭 Find corresponding player.id from api_id
            $playerId = DB::table('players')->where('api_id', $apiId)->value('id');

            if (! $playerId) {
                $this->warn("⏭️ Skipped scorer with api_id {$apiId} — player not found.");
                continue;
            }

            try {
                PredictionCardsMatchScorer::create([
                    'prediction_match_id' => $predictionMatchId,
                    'player_id'           => $playerId,
                    'team_side'           => $teamSide,
                    'minute'              => 40,
                    'type'                => 'goal',
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]);
                $counter++;
            } catch (\Exception $e) {
                $this->warn("⚠️ Failed to insert scorer {$apiId} for match {$predictionMatchId}: {$e->getMessage()}");
            }
        }
    }

    /**
     * Extract numeric IDs from strings like "[333594,37533560]" or "333594,37533560"
     */
    private function extractIds($value): array
    {
        if (!$value) return [];
        $clean = trim($value, "[] \t\n\r\0\x0B");
        if (empty($clean)) return [];
        return array_filter(array_map('trim', explode(',', $clean)), fn($id) => is_numeric($id));
    }
}
