<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\PlayersCard;
use App\Models\Player; // 👈 to check if player exists

class ImportPlayersCardsFromOldDB extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'import:players-cards';

    /**
     * The console command description.
     */
    protected $description = 'Import all PlayersCards from the old database into the new one (keeping original IDs and skipping missing players).';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Starting import of PlayersCards from old database...');

        // Fetch all records from the old database
        $oldRecords = DB::connection('old_mysql')->table('players_cards')->get();

        if ($oldRecords->isEmpty()) {
            $this->warn('⚠️ No records found in old database.');
            return 0;
        }

        $bar = $this->output->createProgressBar($oldRecords->count());
        $bar->start();

        $skipped = 0;

        foreach ($oldRecords as $record) {
            // ✅ Skip record if player not found
            if (!Player::where('id', $record->player_id)->exists()) {
                $skipped++;
                $bar->advance();
                continue;
            }

            // Delete existing record with same ID to avoid conflicts
            PlayersCard::where('id', $record->id)->delete();

            // Insert preserving old ID
            PlayersCard::insert([
                'id' => $record->id,
                'player_id' => $record->player_id,
                'type_id' => $record->type_id ?? null,
                'energy' => $record->energy ?? 10,
                'week_id' => $record->week_id ?? null,
                'stats' => $record->stats ? json_encode($record->stats) : null,
                'created_at' => $record->created_at ?? now(),
                'updated_at' => $record->updated_at ?? now(),
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('✅ PlayersCards import completed successfully (IDs preserved).');

        if ($skipped > 0) {
            $this->warn("⚠️ Skipped {$skipped} records because player_id not found in new database.");
        }

        return 0;
    }
}
