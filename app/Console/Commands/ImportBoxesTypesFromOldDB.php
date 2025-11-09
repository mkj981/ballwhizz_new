<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\BoxesType;

class ImportBoxesTypesFromOldDB extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'import:boxes-types';

    /**
     * The console command description.
     */
    protected $description = 'Import all BoxesType records from the old database into the new one.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Starting import of Boxes Types from old database...');

        $oldRecords = DB::connection('old_mysql')->table('boxes_types')->get();

        if ($oldRecords->isEmpty()) {
            $this->warn('⚠️ No records found in old database.');
            return 0;
        }

        $bar = $this->output->createProgressBar($oldRecords->count());
        $bar->start();

        foreach ($oldRecords as $record) {
            BoxesType::updateOrCreate(
                ['en_name' => $record->en_name],
                [
                    'ar_name' => $record->ar_name,
                    'en_description' => $record->en_description,
                    'ar_description' => $record->ar_description,
                    'time' => $record->time ?? 0,
                    'gold_players' => $record->gold_players ?? 0,
                    'silver_players' => $record->silver_players ?? 0,
                    'bronze_players' => $record->bronze_players ?? 0,
                    'special_players' => $record->special_players ?? 0,
                    'gem' => $record->gem ?? 0,
                    'coins' => $record->coins ?? 0,
                    'xp' => $record->xp ?? 0,
                    'price' => $record->price ?? 0,
                    'swap' => $record->swap ?? 0,
                    'swap_power' => $record->swap_power ?? 0,
                    'gem_cost' => $record->gem_cost ?? 0, // 👈 FIX HERE
                    'image' => $record->image ?? null,
                    'open_image' => $record->open_image ?? null,
                    'en_swap_trade_in_desc' => $record->en_swap_trade_in_desc ?? null,
                    'ar_swap_trade_in_desc' => $record->ar_swap_trade_in_desc ?? null,
                    'en_swap_buy_desc' => $record->en_swap_buy_desc ?? null,
                    'ar_swap_buy_desc' => $record->ar_swap_buy_desc ?? null,
                ]
            );

            $bar->advance();
        }


        $bar->finish();
        $this->newLine(2);
        $this->info('✅ Boxes Types import completed successfully.');

        return 0;
    }
}
