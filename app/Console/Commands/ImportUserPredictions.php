<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\UsersRanking;
use Carbon\Carbon;

class ImportUserPredictions extends Command
{
    protected $signature = 'import:user-predictions
                            {--truncate : Truncate prediction rows before import}';

    protected $description = 'Import old user_predictions into users_rankings table';

    public function handle(): int
    {
        $this->info("🚀 Importing prediction points from OLD DB...");

        /*
        |--------------------------------------------------------------------------
        | OPTIONAL TRUNCATE ONLY PREDICTION ROWS
        |--------------------------------------------------------------------------
        */
        if ($this->option('truncate')) {
            UsersRanking::where('type', 'prediction')->delete();
            $this->warn("⚠️ Deleted existing prediction rows from users_rankings.");
        }

        $this->info("⏳ Starting chunked import...");

        /*
        |--------------------------------------------------------------------------
        | CHUNK PROCESSING - PREVENT MEMORY OVERFLOW
        |--------------------------------------------------------------------------
        */
        DB::connection('old_mysql')
            ->table('user_predictions')
            ->orderBy('id')
            ->chunk(500, function ($rows) {

                foreach ($rows as $old) {

                    /*
                    |--------------------------------------------------------------------------
                    | STEP 1 — Ensure user exists in new DB
                    |--------------------------------------------------------------------------
                    */
                    $userExists = DB::table('users')
                        ->where('id', $old->user_id)
                        ->exists();

                    if (! $userExists) {
                        $this->warn("⚠️ Skip: user_id {$old->user_id} not found.");
                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | STEP 2 — Insert or Update UsersRanking
                    |--------------------------------------------------------------------------
                    */
                    UsersRanking::updateOrCreate(
                        [
                            'user_id'   => $old->user_id,
                            'match_id'  => $old->match_id,
                            'league_id' => $old->league_id,
                            'type'      => 'prediction',
                        ],
                        [
                            'home_team_id'       => $old->home_team_id,
                            'away_team_id'       => $old->away_team_id,

                            'home_team_result'   => $old->home_result,
                            'away_team_result'   => $old->away_result,

                            'home_prediction'    => $old->home_prediction,
                            'away_prediction'    => $old->away_prediction,

                            'points'             => $old->game_points,
                            'scorer_list'        => $old->scorers_points ?? null,

                            // FIX INVALID DATE FORMATS
                            'game_date'          => $this->fixDate($old->game_date),
                            'game_user_date'     => $this->fixDate($old->game_user_date),

                            // Not used for predictions
                            'player_id'          => null,
                            'team_id'            => null,
                            'card_id'            => null,
                            'is_sub'             => 0,
                            'position'           => null,
                        ]
                    );
                }

                $this->info("✔ Processed chunk of " . count($rows) . " records.");
            });

        $this->info("🎉 Import completed successfully!");
        return Command::SUCCESS;
    }

    /*
    |--------------------------------------------------------------------------
    | FIX INVALID DATE FORMAT (e.g. 2025-08-15 17:00:00.000Z)
    |--------------------------------------------------------------------------
    */
    private function fixDate($value)
    {
        if (!$value) {
            return now();
        }

        try {
            return Carbon::parse($value)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return now();
        }
    }
}
