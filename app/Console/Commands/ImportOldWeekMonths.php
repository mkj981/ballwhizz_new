<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\WeekMonth;

class ImportOldWeekMonths extends Command
{
    protected $signature = 'import:old-weekmonths';
    protected $description = 'Import week/month data from old Ballwhizz database into the new week_months table.';

    public function handle(): int
    {
        $this->info('🚀 Starting import of Week/Month data from old database...');

        try {
            // ✅ Fetch records from old DB
            $oldWeeks = DB::connection('old_mysql')->table('week_months')->get();

            if ($oldWeeks->isEmpty()) {
                $this->warn('⚠️ No week/month records found in old database.');
                return Command::SUCCESS;
            }

            $count = 0;

            foreach ($oldWeeks as $old) {
                // ✅ Use "start" and "end" columns
                $rawStart = $old->start ?? null;
                $rawEnd   = $old->end ?? null;

                if (!$rawStart || !$rawEnd) {
                    $this->warn("⚠️ Skipped record (missing start/end date) for week #{$old->week}");
                    continue;
                }

                // ✅ Convert YYYY-MM-DD → YYYY-MM-DD 00:00:00 / 23:59:00
                $startDate = date('Y-m-d 00:00:00', strtotime($rawStart));
                $endDate   = date('Y-m-d 23:59:00', strtotime($rawEnd));

                // ✅ Default name if missing
                $weekName = 'Week ' . ($old->week ?? '—');

                // ✅ Insert or update
                WeekMonth::updateOrCreate(
                    ['week' => $old->week],
                    [
                        'week_name'  => $weekName,
                        'start_date' => $startDate,
                        'end_date'   => $endDate,
                    ]
                );

                $count++;
            }

            $this->info("✅ Successfully imported {$count} week records.");

        } catch (\Exception $e) {
            $this->error('❌ Import failed: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
