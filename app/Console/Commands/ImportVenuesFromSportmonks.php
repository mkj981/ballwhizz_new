<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Venues;

class ImportVenuesFromSportmonks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Usage:
     *   php artisan import:venues
     */
    protected $signature = 'import:venues';

    /**
     * The console command description.
     */
    protected $description = 'Import all football venues from SportMonks API with full pagination.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $page = 1;
        $total = 0;

        $this->info("🚀 Starting import of venues from SportMonks...");

        while (true) {
            $this->line("📄 Fetching page {$page}...");

            $response = $this->getVenues($page);

            if (empty($response['data'])) {
                $this->warn("⚠️ No data found on page {$page}. Stopping import.");
                break;
            }

            foreach ($response['data'] as $venue) {
                $model = Venues::find($venue['id']) ?? new Venues();
                $model->id = $venue['id']; // ✅ preserve SportMonks ID
                $model->country_id = $venue['country_id'] ?? null;
                $model->city_id = $venue['city_id'] ?? null;
                $model->name = $venue['name'] ?? null;
                $model->address = $venue['address'] ?? null;
                $model->zipcode = $venue['zipcode'] ?? null;
                $model->latitude = $venue['latitude'] ?? null;
                $model->longitude = $venue['longitude'] ?? null;
                $model->capacity = $venue['capacity'] ?? null;
                $model->image_path = $venue['image_path'] ?? null;
                $model->city_name = $venue['city_name'] ?? null;
                $model->surface = $venue['surface'] ?? null;
                $model->national_team = $venue['national_team'] ?? false;
                $model->status = true;
                $model->save();

                $total++;
            }

            $count = count($response['data']);
            $this->info("✅ Imported {$count} venues from page {$page}");

            // 🧭 Pagination logic
            $pagination = $response['pagination'] ?? [];
            $hasMore = $pagination['has_more'] ?? false;
            $nextPageUrl = $pagination['next_page'] ?? null;

            if (!$hasMore || empty($nextPageUrl)) {
                $this->info("🏁 No more pages after {$page}. Import completed.");
                break;
            }

            // Extract next page number from URL
            $nextPage = $this->extractPageNumber($nextPageUrl);
            if (!$nextPage) {
                $this->warn("⚠️ Could not detect next page number, stopping at page {$page}.");
                break;
            }

            $page = $nextPage;
            $this->line("➡️ Moving to page {$page}...");
            sleep(1); // avoid hitting rate limits
        }

        $this->info("🎉 Successfully imported {$total} venues from SportMonks!");
        return Command::SUCCESS;
    }

    /**
     * Fetch a page of venues from SportMonks API.
     */
    protected function getVenues(int $page): ?array
    {
        $token = env('SPORTMONKS_API_TOKEN');
        $url = "https://api.sportmonks.com/v3/football/venues?page={$page}&api_token={$token}";

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
        return isset($query['page']) ? (int) $query['page'] : null;
    }
}
