<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Countries;

class ImportCountriesFromSportmonks extends Command
{
    protected $signature = 'import:countries';
    protected $description = 'Import ALL countries from Sportmonks API with working pagination.';

    public function handle(): int
    {
        $page = 1;
        $this->info('🌍 Starting full import of countries from Sportmonks...');

        while (true) {
            $this->line("📄 Fetching page {$page}...");

            $response = $this->getCountry($page);

            if (empty($response['data'])) {
                $this->warn("⚠️ No countries found on page {$page}. Stopping import.");
                break;
            }

            // 🗂️ Save or update countries
            foreach ($response['data'] as $country) {
                $model = Countries::find($country['id']) ?? new Countries();
                $model->id = $country['id'];
                $model->en_name = $country['official_name'] ?? $country['name'];
                $model->ar_name = $country['name'] ?? null;
                $model->continent_id = $country['continent_id'] ?? null;
                $model->fifa_name = $country['fifa_name'] ?? null;
                $model->iso2 = $country['iso2'] ?? null;
                $model->iso3 = $country['iso3'] ?? null;
                $model->latitude = $country['latitude'] ?? null;
                $model->longitude = $country['longitude'] ?? null;
                $model->borders = !empty($country['borders']) ? json_encode($country['borders']) : null;
                $model->image_path = $country['image_path'] ?? null;
                $model->save();
            }

            $count = count($response['data']);
            $this->info("✅ Imported {$count} countries from page {$page}");

            // 🧭 Handle pagination correctly
            $pagination = $response['pagination'] ?? [];
            $hasMore = $pagination['has_more'] ?? false;
            $nextPageUrl = $pagination['next_page'] ?? null;

            if (!$hasMore || empty($nextPageUrl)) {
                $this->info('🏁 No more pages found. Import completed.');
                break;
            }

            // ✅ Extract next page number from URL
            $page = $this->extractPageNumber($nextPageUrl);

            if (!$page) {
                $this->warn('⚠️ Could not detect next page number. Stopping import.');
                break;
            }

            sleep(1); // prevent rate limits
        }

        $this->info('🎉 All countries imported successfully!');
        return Command::SUCCESS;
    }

    /**
     * Fetch a page of countries from Sportmonks API.
     */
    protected function getCountry(int $page): ?array
    {
        $token = config('services.sportmonks.token', 'esEnL4WkxrGr46O6ABq99INL1Eww4iTDYz8rtABQwapL3QX8ujNiktU4hnH0');
        $url = "https://api.sportmonks.com/v3/core/countries?api_token={$token}&locale=ar&page={$page}";

        $response = Http::timeout(30)->get($url);

        if ($response->failed()) {
            $this->error("❌ Failed to fetch page {$page}");
            return [];
        }

        return $response->json();
    }

    /**
     * Extract page number from next_page URL (e.g. ...page=2)
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
