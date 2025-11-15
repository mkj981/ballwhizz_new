<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\OddsMarket;

class OddsImportMarkets extends Command
{
    protected $signature = 'odds:import-markets {--truncate}';
    protected $description = 'Import odds markets from SportMonks';

    public function handle(): int
    {
        $this->info("🚀 Importing odds markets...");

        if ($this->option('truncate')) {
            OddsMarket::truncate();
            $this->warn("⚠️ odds_markets table truncated.");
        }

        $page = 1;

        do {
            $this->info("📄 Fetching page {$page}...");

            $response = Http::get("https://api.sportmonks.com/v3/odds/markets", [
                'api_token' => env('SPORTMONKS_API_TOKEN'),
                'page'      => $page,
            ]);

            if ($response->failed()) {
                $this->error("❌ Failed fetching markets.");
                return Command::FAILURE;
            }

            $json = $response->json();

            foreach ($json['data'] ?? [] as $item) {
                OddsMarket::updateOrCreate(
                    ['legacy_id' => $item['legacy_id']],
                    [
                        'name' => $item['name'],
                        'developer_name' => $item['developer_name'] ?? null,
                        'has_winning_calculations' => $item['has_winning_calculations'] ?? 0,
                    ]
                );

                $this->info("✔ Imported: {$item['name']} ({$item['legacy_id']})");
            }

            $hasMore = $json['pagination']['has_more'] ?? false;
            $page++;

        } while ($hasMore);

        $this->info("🎉 Markets import completed!");
        return Command::SUCCESS;
    }
}
