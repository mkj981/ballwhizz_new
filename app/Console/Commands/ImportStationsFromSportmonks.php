<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Stations;

class ImportStationsFromSportmonks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Usage:
     *   php artisan import:stations
     */
    protected $signature = 'import:stations';

    /**
     * The console command description.
     */
    protected $description = 'Import all football TV stations from SportMonks API with full pagination.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $page = 1;
        $total = 0;

        $this->info("🚀 Starting import of TV Stations from SportMonks...");

        while (true) {
            $this->line("📄 Fetching page {$page}...");

            $response = $this->getStations($page);

            if (empty($response['data'])) {
                $this->warn("⚠️ No data found on page {$page}. Stopping import.");
                break;
            }

            foreach ($response['data'] as $station) {
                $model = Stations::find($station['id']) ?? new Stations();
                $model->id = $station['id']; // Preserve original SportMonks ID
                $model->name = $station['name'] ?? null;
                $model->url = $station['url'] ?? null;
                $model->image_path = $station['image_path'] ?? null;
                $model->type = $station['type'] ?? null;
                $model->related_id = $station['related_id'] ?? null;
                $model->status = true;
                $model->save();

                $total++;
            }

            $count = count($response['data']);
            $this->info("✅ Imported {$count} stations from page {$page}");

            $pagination = $response['pagination'] ?? [];
            $hasMore = $pagination['has_more'] ?? false;
            $nextPageUrl = $pagination['next_page'] ?? null;

            if (!$hasMore || empty($nextPageUrl)) {
                $this->info("🏁 No more pages after {$page}. Import completed.");
                break;
            }

            $nextPage = $this->extractPageNumber($nextPageUrl);
            if (!$nextPage) {
                $this->warn("⚠️ Could not detect next page number, stopping at page {$page}.");
                break;
            }

            $page = $nextPage;
            $this->line("➡️ Moving to page {$page}...");
            sleep(1); // avoid rate limit
        }

        $this->info("🎉 Successfully imported {$total} TV Stations from SportMonks!");
        return Command::SUCCESS;
    }

    /**
     * Fetch a page of stations from SportMonks API.
     */
    protected function getStations(int $page): ?array
    {
        $token = env('SPORTMONKS_API_TOKEN');
        $url = "https://api.sportmonks.com/v3/football/tv-stations?page={$page}&api_token={$token}";

        $response = Http::timeout(60)->get($url);

        if ($response->failed()) {
            $this->error("❌ API request failed on page {$page}");
            return [];
        }

        return $response->json();
    }

    /**
     * Extract page number from SportMonks next_page URL.
     */
    protected function extractPageNumber(?string $url): ?int
    {
        if (empty($url)) return null;

        $parts = parse_url($url);
        if (!isset($parts['query'])) return null;

        parse_str($parts['query'], $query);
        return isset($query['page']) ? (int)$query['page'] : null;
    }
}
