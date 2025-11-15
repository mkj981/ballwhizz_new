<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\OddsBookmaker;

class OddsImportBookmakers extends Command
{
    protected $signature = 'odds:import-bookmakers
                            {--truncate : Truncate the table before importing}';

    protected $description = 'Import bookmakers from SportMonks Odds API';

    public function handle(): int
    {
        $this->info('🚀 Importing bookmakers from SportMonks...');

        if ($this->option('truncate')) {
            OddsBookmaker::truncate();
            $this->warn('⚠️ Table truncated.');
        }

        $page = 1;

        do {
            $this->info("📄 Fetching page $page ...");

            $response = Http::get('https://api.sportmonks.com/v3/odds/bookmakers', [
                'api_token' => env('SPORTMONKS_API_TOKEN'),
                'page'      => $page,
            ]);

            if ($response->failed()) {
                $this->error('❌ SportMonks request failed.');
                return Command::FAILURE;
            }

            $json = $response->json();

            foreach ($json['data'] ?? [] as $item) {
                OddsBookmaker::updateOrCreate(
                    ['legacy_id' => $item['legacy_id']],
                    ['name' => $item['name']]
                );
                $this->info("✔ Imported: {$item['name']} (Legacy {$item['legacy_id']})");
            }

            $hasMore = $json['pagination']['has_more'] ?? false;
            $page++;

        } while ($hasMore);

        $this->info('🎉 Import complete!');
        return Command::SUCCESS;
    }
}
