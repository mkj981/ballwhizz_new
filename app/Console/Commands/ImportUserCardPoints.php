<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\CardsWeek;
use App\Models\UsersRanking;
use Carbon\Carbon;

class ImportUserCardPoints extends Command
{
    protected $signature = 'import:user-card-points
                            {--truncate : Truncate new table before import}';

    protected $description = 'Import user card points from OLD DB into users_rankings table';

    public function handle(): int
    {
        $this->info("🚀 Starting import from OLD database...");

        // Optional truncate
        if ($this->option('truncate')) {
            UsersRanking::truncate();
            $this->warn("⚠️ users_rankings table truncated.");
        }

        // Fetch data from old DB
        $oldRecords = DB::connection('old_mysql')
            ->table('user_card_points')
            ->orderBy('id')
            ->get();

        $this->info("📦 Found {$oldRecords->count()} records to process.");

        foreach ($oldRecords as $old) {

            /*
            |--------------------------------------------------------------------------
            | STEP 1 — USER CHECK
            |--------------------------------------------------------------------------
            */
            $userExists = DB::table('users')
                ->where('id', $old->user_id)
                ->exists();

            if (! $userExists) {
                $this->warn("⚠️ Skipped: user_id {$old->user_id} does not exist.");
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | STEP 2 — CARD CHECK
            |--------------------------------------------------------------------------
            */
            $card = DB::table('players_cards')
                ->where('id', $old->card_id)
                ->first();

            if (! $card) {
                $this->warn("⚠️ Skipped: card_id {$old->card_id} does not exist.");
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | STEP 3 — PLAYER FROM CARD
            |--------------------------------------------------------------------------
            */
            $player = DB::table('players')
                ->where('id', $card->player_id)
                ->first();

            if (! $player) {
                $this->warn("⚠️ Skipped: player_id {$card->player_id} not found for card {$old->card_id}");
                continue;
            }

            $player_id   = $player->id;
            $team_id     = $player->team_id;
            $position_id = $player->position_id;

            /*
            |--------------------------------------------------------------------------
            | STEP 4 — FIND CardsWeek BY matchday + league_id
            |--------------------------------------------------------------------------
            */
            $cardsWeek = CardsWeek::where('matchday', $old->week_name)
                ->where('league_id', $old->league_id)
                ->first();

            if (! $cardsWeek) {
                $this->warn("⚠️ Skipped: CardsWeek not found for week {$old->week_name}, league {$old->league_id}");
                continue;
            }

            $cards_week_id = $cardsWeek->id;
            $prediction_week_id = $cardsWeek->week_months_id;

            /*
            |--------------------------------------------------------------------------
            | STEP 5 — INSERT OR UPDATE
            |--------------------------------------------------------------------------
            */
            UsersRanking::updateOrCreate(
                [
                    'user_id'       => $old->user_id,
                    'match_id'      => $old->match_id,
                    'league_id'     => $old->league_id,
                    'card_id'       => $old->card_id,
                    'cards_week_id' => $cards_week_id,
                ],
                [
                    'points'                => $old->points,
                    'prediction_week_id'    => $prediction_week_id,
                    'type'                  => 'cards',

                    // NEW: Player data
                    'player_id'             => $player_id,
                    'team_id'               => $team_id,
                    'position'              => $position_id,

                    // Default
                    'scorer_list'           => null,
                    'home_team_result'      => null,
                    'away_team_result'      => null,
                    'is_sub'                => 0,

                    // NEW: Use OLD created_at
                    'game_date'             => $old->created_at,
                    'game_user_date'        => $old->created_at,
                ]
            );

            $this->info("✔ Imported/Updated | User {$old->user_id} | Card {$old->card_id} | Player {$player_id} | Match {$old->match_id}");
        }

        $this->info("🎉 Import completed successfully!");
        return Command::SUCCESS;
    }
}
