<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\CardsWeek;
use Carbon\Carbon;

class ImportOldCardsWeeks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Usage:
     *   php artisan import:old-cards-weeks
     *   php artisan import:old-cards-weeks --truncate
     */
    protected $signature = 'import:old-cards-weeks {--truncate : Truncate the cards_weeks table before importing}';

    /**
     * The console command description.
     */
    protected $description = '🕹️ Import cards weeks from old database (cards_weeeks table) into new cards_weeks table.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Starting import from old table [cards_weeeks]...');

        $oldConnection = 'old_mysql'; // ⚙️ must exist in config/database.php

        // Optionally truncate existing records
        if ($this->option('truncate')) {
            CardsWeek::truncate();
            $this->warn('⚠️ Truncated existing cards_weeks records.');
        }

        // Fetch data from old DB
        $oldRecords = DB::connection($oldConnection)->table('cards_weeeks')->get();

        $this->info("📦 Found {$oldRecords->count()} records to import...");

        $imported = 0;

        foreach ($oldRecords as $old) {
            try {
                CardsWeek::create([
                    'week_months_id' => $old->prediction_week_id,
                    'league_id'      => $old->league_id,
                    'matchday'       => $old->this_week,
                    'start'          => $this->formatDate($old->start),
                    'end'            => $this->formatDate($old->end),
                    'close_at'       => $this->formatDate($old->close_at),
                    'is_active'      => (bool) $old->is_active,
                    'is_open'        => (bool) $old->is_open,
                ]);

                $imported++;
            } catch (\Exception $e) {
                $this->error("❌ Failed to import record ID {$old->id}: " . $e->getMessage());
            }
        }

        $this->info("✅ Successfully imported {$imported} records into cards_weeks.");
        return Command::SUCCESS;
    }

    /**
     * Convert ISO-like string (e.g., 2025-08-15T00:01) to MySQL datetime (Y-m-d H:i:s).
     */
    private function formatDate($value): ?string
    {
        if (!$value) return null;

        try {
            return Carbon::parse(str_replace('T', ' ', $value))->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }
}
