<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\CardType;

class ImportCardTypesFromOldDB extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'import:card-types';

    /**
     * The console command description.
     */
    protected $description = 'Import all CardTypes from the old database into the new one (preserving IDs).';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Starting import of Card Types (keeping old IDs)...');

        $oldRecords = DB::connection('old_mysql')->table('card_types')->get();

        if ($oldRecords->isEmpty()) {
            $this->warn('⚠️ No records found in old database.');
            return 0;
        }

        $bar = $this->output->createProgressBar($oldRecords->count());
        $bar->start();

        foreach ($oldRecords as $record) {
            // Delete if ID already exists (to avoid duplicate key error)
            CardType::where('id', $record->id)->delete();

            CardType::insert([
                'id' => $record->id,
                'en_name' => $record->en_name ?? '',
                'ar_name' => $record->ar_name ?? '',
                'multiplier' => $record->multiplier ?? 1.0,
                'image' => $record->image ?? null,
                'created_at' => $record->created_at ?? now(),
                'updated_at' => $record->updated_at ?? now(),
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('✅ Card Types imported successfully with original IDs preserved!');

        return 0;
    }
}
