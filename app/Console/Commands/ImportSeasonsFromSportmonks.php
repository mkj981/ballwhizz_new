<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Seasons;

class ImportSeasonsFromSportmonks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Usage:
     *   php artisan import:seasons
     */
    protected $signature = 'import:seasons';

    /**
     * The console command description.
     */
    protected $description = 'Import all football seasons from SportMonks API with full pagination.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $page = 1;
        $total = 0;

        $this->info("🚀 Starting import of seasons from SportMonks...");

        while (true) {
            $this->line("📄 Fetching page {$page}...");

            $response = $this->getSeasons($page);

            if (empty($response['data'])) {
                $this->warn("⚠️ No data found on page {$page}. Stopping import.");
                break;
            }

            foreach ($response['data'] as $season) {
                $model = Seasons::find($season['id']) ?? new Seasons();
                $model->id = $season['id']; // ✅ preserve SportMonks ID
                $model->league_id = $season['league_id'] ?? null;
                $model->tie_breaker_rule_id = $season['tie_breaker_rule_id'] ?? null;
                $model->name = $season['name'] ?? null;
                $model->finished = $season['finished'] ?? false;
                $model->pending = $season['pending'] ?? false;
                $model->is_current = $season['is_current'] ?? false;
                $model->starting_at = $season['starting_at'] ?? null;
                $model->ending_at = $season['ending_at'] ?? null;
                $model->standings_recalculated_at = $season['standings_recalculated_at'] ?? null;
                $model->status = true; // default to active
                $model->save();
                $total++;
            }

            $count = count($response['data']);
            $this->info("✅ Imported {$count} seasons from page {$page}");

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

        $this->info("🎉 Successfully imported {$total} seasons from SportMonks!");
        return Command::SUCCESS;
    }

    /**
     * Fetch a page of seasons from SportMonks API.
     */
    protected function getSeasons(int $page): ?array
    {
        $token = config('services.sportmonks.token', 'esEnL4WkxrGr46O6ABq99INL1Eww4iTDYz8rtABQwapL3QX8ujNiktU4hnH0');
        $url = "https://api.sportmonks.com/v3/football/seasons?api_token={$token}&page={$page}";

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
