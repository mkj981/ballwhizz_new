<?php

namespace App\Console\Commands;

use App\Models\Leagues;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;


class ImportLeaguesFromSportmonks extends Command
{
    protected $signature = 'import:leagues {lang=en}';
    protected $description = 'Import leagues from Sportmonks API (English or Arabic) with full pagination and proper ID mapping.';

    public function handle(): int
    {
        $lang = $this->argument('lang');
        $page = 1;
        $totalImported = 0;

        $this->info("🚀 Importing leagues from Sportmonks (lang: {$lang}) ...");

        while (true) {
            $this->line("📄 Fetching page {$page}...");

            $response = $this->getLeagues($page, $lang);

            if (empty($response['data'])) {
                $this->warn("⚠️ No leagues found on page {$page}. Stopping import.");
                break;
            }

            foreach ($response['data'] as $league) {
                $model = Leagues::find($league['id']) ?? new Leagues();
                $model->id = $league['id']; // ✅ preserve SportMonks ID
                $model->country_id   = $league['country_id'] ?? null;
                $model->type         = $league['type'] ?? null;
                $model->sub_type     = $league['sub_type'] ?? null;
                $model->short_code   = isset($league['short_code']) ? substr($league['short_code'], 0, 50) : null; // safe length
                $model->category     = $league['category'] ?? null;
                $model->image_path   = $league['image_path'] ?? null;
                $model->status       = $league['active'] ?? true;
                $model->cards_status = $league['has_jerseys'] ?? false;

                // ✅ handle multilingual names
                if ($lang === 'ar') {
                    $model->ar_name = $league['name'];
                } else {
                    $model->en_name = $league['name'];
                }

                $model->save();
                $totalImported++;
            }

            $count = count($response['data']);
            $this->info("✅ Imported {$count} leagues from page {$page}");

            // 🧭 Pagination handling
            $pagination = $response['pagination'] ?? [];
            $hasMore = $pagination['has_more'] ?? false;
            $nextPageUrl = $pagination['next_page'] ?? null;

            if (!$hasMore || empty($nextPageUrl)) {
                $this->info("🏁 No more pages after {$page}. Import complete.");
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
            sleep(1); // avoid rate limit
        }

        $this->info("🎉 Finished importing leagues! Total: {$totalImported}");
        return Command::SUCCESS;
    }

    protected function getLeagues(int $page, string $lang): ?array
    {
        $token = config('services.sportmonks.token', 'esEnL4WkxrGr46O6ABq99INL1Eww4iTDYz8rtABQwapL3QX8ujNiktU4hnH0');
        $url = "https://api.sportmonks.com/v3/football/leagues?api_token={$token}&locale={$lang}&page={$page}";

        $response = Http::timeout(60)->get($url);

        if ($response->failed()) {
            $this->error("❌ API request failed on page {$page}");
            return [];
        }

        return $response->json();
    }

    protected function extractPageNumber(?string $url): ?int
    {
        if (empty($url)) return null;

        $parts = parse_url($url);
        if (!isset($parts['query'])) return null;

        parse_str($parts['query'], $query);
        return isset($query['page']) ? (int) $query['page'] : null;
    }
}
