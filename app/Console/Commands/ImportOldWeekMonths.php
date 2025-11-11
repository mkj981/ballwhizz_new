<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\WeekMonth;
use Carbon\Carbon;

class ImportOldWeekMonths extends Command
{
    protected $signature = 'import:old-weekmonths {--truncate : Truncate week_months before importing}';
    protected $description = '🗓️ Import week/month data from old Ballwhizz database into the new week_months table, keeping original IDs.';

    public function handle(): int
    {
        $this->info('🚀 Starting import of Week/Month data from old database...');

        try {
            $oldConnection = 'old_mysql'; // ⚙️ Must exist in config/database.php

            // ✅ Optionally clear existing records
            if ($this->option('truncate')) {
                DB::statement('DELETE FROM week_months');
                $this->warn('⚠️ Deleted existing week_months records.');
            }

            // ✅ Fetch all old week/month records
            $oldWeeks = DB::connection($oldConnection)->table('week_months')->get();

            if ($oldWeeks->isEmpty()) {
                $this->warn('⚠️ No week/month records found in old database.');
                return Command::SUCCESS;
            }

            $count = 0;

            foreach ($oldWeeks as $old) {
                // ✅ Prepare date values
                $start = $this->formatDate($old->start ?? null, true);
                $end   = $this->formatDate($old->end ?? null, false);

                if (!$start || !$end) {
                    $this->warn("⚠️ Skipped ID {$old->id} — missing or invalid dates.");
                    continue;
                }

                // ✅ Use the same ID and insert or update
                $exists = WeekMonth::find($old->id);

                if ($exists) {
                    $exists->update([
                        'week'       => $old->week ?? null,
                        'week_name'  => $old->week_name ?? ('Week ' . ($old->week ?? '—')),
                        'start_date' => $start,
                        'end_date'   => $end,
                    ]);
                } else {
                    DB::table('week_months')->insert([
                        'id'         => $old->id,
                        'week'       => $old->week ?? null,
                        'week_name'  => $old->week_name ?? ('Week ' . ($old->week ?? '—')),
                        'start_date' => $start,
                        'end_date'   => $end,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $count++;
            }

            $this->info("✅ Successfully imported {$count} week/month records with original IDs.");

        } catch (\Exception $e) {
            $this->error('❌ Import failed: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Convert date (YYYY-MM-DD) or ISO (2025-08-15T00:01) into proper MySQL datetime.
     */
    private function formatDate(?string $value, bool $isStart = true): ?string
    {
        if (!$value) return null;

        try {
            $carbon = Carbon::parse(str_replace('T', ' ', $value));
            return $isStart
                ? $carbon->startOfDay()->format('Y-m-d H:i:s')
                : $carbon->endOfDay()->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }
}
