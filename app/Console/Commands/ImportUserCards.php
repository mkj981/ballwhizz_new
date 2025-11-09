<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\UserCard;
use App\Models\Positions;

class ImportUserCards extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Usage:
     *   php artisan import:user-cards
     *   php artisan import:user-cards --truncate
     */
    protected $signature = 'import:user-cards {--truncate : Truncate the user_cards table before importing}';

    /**
     * The console command description.
     */
    protected $description = '🃏 Import user cards from the old database into the new user_cards table.';

    public function handle(): int
    {
        $oldConnection = 'old_mysql'; // ⚙️ must exist in config/database.php
        $this->info("🚀 Starting import of user cards from old database...");

        // Optional truncate
        if ($this->option('truncate')) {
            UserCard::truncate();
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

        // ✅ Import safely in chunks to avoid memory overload
        $query->orderBy('id')->chunk(1000, function ($chunk) use ($bar) {
            foreach ($chunk as $old) {
                try {
                    // 🧩 Validate position_id
                    $positionId = $old->position_id ?? null;
                    if ($positionId && !Positions::find($positionId)) {
                        $this->warn("\n⚠️ Skipping invalid position_id {$positionId} for user_id {$old->user_id}");
                        $positionId = null;
                    }

                    // 🃏 Create or update record
                    UserCard::updateOrCreate(
                        [
                            'user_id' => $old->user_id,
                            'card_id' => $old->card_id,
                        ],
                        [
                            'league_id'   => $old->league_id ?? null,
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
        $this->info("✅ Import completed successfully!");
        return Command::SUCCESS;
    }
}
