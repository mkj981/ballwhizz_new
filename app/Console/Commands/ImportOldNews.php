<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\News;

class ImportOldNews extends Command
{
    protected $signature = 'import:old-news {--truncate : Truncate the current news table before import}';
    protected $description = '📰 Import all news records from the old Ballwhizz database into the new one.';

    public function handle(): int
    {
        $this->info('🚀 Starting news import from old database...');

        $old = DB::connection('old_mysql');
        $new = DB::connection('mysql');

        if ($this->option('truncate')) {
            $this->warn('⚠️ Truncating new `news` table before import...');
            $new->table('news')->truncate();
        }

        $oldRecords = $old->table('news')->get();

        if ($oldRecords->isEmpty()) {
            $this->warn('❌ No records found in old database.');
            return Command::FAILURE;
        }

        $bar = $this->output->createProgressBar($oldRecords->count());
        $bar->start();

        $count = 0;

        foreach ($oldRecords as $record) {
            try {
                News::create([
                    'en_title'        => $record->en_title ?? '',
                    'ar_title'        => $record->ar_title ?? '',
                    'en_text'         => $record->en_text ?? '',
                    'ar_text'         => $record->ar_text ?? '',
                    'en_short_desc'   => $record->en_short_desc ?? '',
                    'ar_short_desc'   => $record->ar_short_desc ?? '',
                    'hashtags'        => $record->hashtags ?? '',
                    'video'           => $record->video ?? '',
                    // ✅ Keep the original path as-is
                    'image'           => $record->image ?? null,
                    'created_at'      => $record->created_at,
                    'updated_at'      => $record->updated_at,
                ]);

                $count++;
                $bar->advance();
            } catch (\Throwable $e) {
                $this->error("\n❌ Failed to import record ID {$record->id}: {$e->getMessage()}");
            }
        }

        $bar->finish();
        $this->info("\n✅ Import completed successfully! Imported {$count} records.");

        return Command::SUCCESS;
    }
}
