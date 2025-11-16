<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\AppNews;

class ImportAppNews extends Command
{
    protected $signature = 'import:app-news
                            {--truncate : Truncate appnews table before import}';

    protected $description = 'Import App News from OLD DB into new appnews table';

    public function handle(): int
    {
        $this->info("🚀 Importing App News from OLD DB...");

        /*
        |--------------------------------------------------------------------------
        | OPTIONAL TRUNCATE
        |--------------------------------------------------------------------------
        */
        if ($this->option('truncate')) {
            AppNews::truncate();
            $this->warn("⚠️ appnews table truncated.");
        }

        /*
        |--------------------------------------------------------------------------
        | READ OLD DB TABLE (old_mysql.app_news)
        |--------------------------------------------------------------------------
        */
        DB::connection('old_mysql')
            ->table('app_news')
            ->orderBy('id')
            ->chunk(200, function ($rows) {

                foreach ($rows as $old) {

                    /*
                    |--------------------------------------------------------------------------
                    | FIX IMAGES FORMAT (DB stores JSON string or comma-separated)
                    |--------------------------------------------------------------------------
                    */
                    $imagesArray = [];

                    if (!empty($old->images)) {
                        // Try JSON decode first
                        $decoded = json_decode($old->images, true);

                        if (is_array($decoded)) {
                            $imagesArray = $decoded;
                        } else {
                            // fallback: comma-separated string
                            $imagesArray = array_map('trim', explode(',', $old->images));
                        }
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | INSERT OR UPDATE
                    |--------------------------------------------------------------------------
                    */
                    AppNews::updateOrCreate(
                        ['id' => $old->id],   // preserve old IDs
                        [
                            'short_text_en' => $old->short_text_en,
                            'short_text_ar' => $old->short_text_ar,
                            'long_text_en'  => $old->long_text_en,
                            'long_text_ar'  => $old->long_text_ar,
                            'video_url'     => $old->video_url,
                            'images'        => $imagesArray,
                            'created_at'    => $old->created_at,
                            'updated_at'    => $old->updated_at,
                        ]
                    );

                    $this->info("✔ Imported: AppNews ID {$old->id}");
                }
            });

        $this->info("🎉 App News import completed successfully!");
        return Command::SUCCESS;
    }
}
