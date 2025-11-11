<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\UserCard;
use App\Models\Positions;

class ImportUserCards extends Command
{
    protected $signature = 'import:user-cards {--truncate : Truncate the user_cards table before importing}';
    protected $description = '🃏 Import user cards from the old database into the new user_cards table.';

    public function handle(): int
    {
        $oldConnection = 'old_mysql';
        $this->info("🚀 Starting import of user cards from old database...");

        // Optional truncate
        if ($this->option('truncate')) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            UserCard::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->warn("⚠️ Cleared existing records from user_cards table.");
        }

        // Count records
        $query = DB::connection($oldConnection)->table('user_cards');
        $total = $query->count();

        if ($total === 0) {
            $this->warn("⚠️ No user_cards found in old database.");
            return Command::SUCCESS;
        }

        $this->info("📦 Found {$total} user card records to import...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        // ✅ Import in chunks
        $query->orderBy('id')->chunk(1000, function ($chunk) use ($bar) {
            foreach ($chunk as $old) {
                try {
                    // 🧩 Validate or fallback position_id
                    $positionId = $old->position_id ?? null;
                    if (!$positionId || !Positions::where('id', $positionId)->exists()) {
                        $this->warn("\n⚠️ Invalid or missing position_id ({$positionId}) for user_id {$old->user_id}, using default (27).");
                        $positionId = 27; // fallback ID
                    }

                    // ✅ Validate foreign keys
                    $userExists = DB::table('users')->where('id', $old->user_id)->exists();
                    $cardExists = DB::table('players_cards')->where('id', $old->card_id)->exists();
                    $leagueExists = $old->league_id
                        ? DB::table('leagues')->where('id', $old->league_id)->exists()
                        : true;

                    if (!$userExists || !$cardExists) {
                        $this->warn("\n⚠️ Skipped: user_id {$old->user_id} or card_id {$old->card_id} missing.");
                        continue;
                    }

                    // 🃏 Insert or update safely
                    UserCard::updateOrCreate(
                        [
                            'user_id' => $old->user_id,
                            'card_id' => $old->card_id,
                        ],
                        [
                            'league_id'   => $leagueExists ? $old->league_id : null,
                            'position_id' => $positionId,
                            'is_in_team'  => (bool) ($old->is_in_team ?? 0),
                            'is_sub'      => (bool) ($old->is_sub ?? 0),
                            'in_stad'     => (int) ($old->in_stad ?? 0),
                            'created_at'  => $old->created_at ?? now(),
                            'updated_at'  => $old->updated_at ?? now(),
                        ]
                    );
                } catch (\Throwable $e) {
                    $this->error("\n❌ Error importing record (user_id: {$old->user_id}, card_id: {$old->card_id}): {$e->getMessage()}");
                }

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ Import completed successfully with fallback position_id=27!");
        return Command::SUCCESS;
    }
}
