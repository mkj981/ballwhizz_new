<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\UsersRanking;
use Carbon\Carbon;

class ImportTriviaDay extends Command
{
    protected $signature = 'import:trivia-day
                            {--truncate : Truncate trivia rows before import}';

    protected $description = 'Import Trivia Day points from old DB into users_rankings table';

    public function handle(): int
    {
        $this->info("🚀 Importing Trivia Day points from OLD DB...");

        if ($this->option('truncate')) {
            UsersRanking::where('type', 'trivia')->delete();
            $this->warn("⚠️ Deleted existing trivia rows from users_rankings.");
        }

        $this->info("⏳ Starting chunked import...");

        DB::connection('old_mysql')
            ->table('usertriviaday')
            ->orderBy('id')
            ->chunk(500, function ($rows) {

                foreach ($rows as $old) {

                    // Check user exists
                    if (!DB::table('users')->where('id', $old->users_id)->exists()) {
                        $this->warn("⚠️ Skip: user_id {$old->users_id} not found.");
                        continue;
                    }

                    // Create new entry — NOT updateOrCreate
                    UsersRanking::create([
                        'user_id'            => $old->users_id,
                        'match_id'           => null,
                        'league_id'          => null,
                        'type'               => 'trivia',

                        'points'             => $old->points ?? 0,

                        'home_team_id'       => null,
                        'away_team_id'       => null,
                        'home_team_result'   => null,
                        'away_team_result'   => null,
                        'home_prediction'    => null,
                        'away_prediction'    => null,
                        'scorer_list'        => null,

                        'game_date'          => $this->fixDate($old->created_at),
                        'game_user_date'     => $this->fixDate($old->updated_at),

                        'player_id'          => null,
                        'team_id'            => null,
                        'card_id'            => null,
                        'is_sub'             => 0,
                        'position'           => null,
                        'position_in_app'    => null,
                    ]);
                }

                $this->info("✔ Imported chunk (" . count($rows) . " rows)");
            });

        $this->info("🎉 Trivia import completed successfully!");
        return Command::SUCCESS;
    }

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
