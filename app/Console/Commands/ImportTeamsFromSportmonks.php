<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Teams;

class ImportTeamsFromSportmonks extends Command
{
    protected $signature = 'import:teams {lang=en}';
    protected $description = 'Import all football teams from SportMonks API with fallback to Unknown Country (continent 5) and Unknown Venue.';

    public function handle(): int
    {
        $lang  = $this->argument('lang');
        $page  = 1;
        $total = 0;

        $this->info("🚀 Starting import of teams from SportMonks (lang: {$lang})...");

        // ✅ Ensure fallback country & venue exist (using continent_id = 5)
        [$unknownCountryId, $unknownVenueId] = $this->ensureUnknownRecords();

        while (true) {
            $this->line("📄 Fetching page {$page}...");
            $response = $this->getTeams($page, $lang);

            if (empty($response['data'])) {
                $this->warn("⚠️ No data found on page {$page}. Stopping import.");
                break;
            }

            foreach ($response['data'] as $team) {
                // ✅ Safe country
                $countryId = $team['country_id'] ?? $unknownCountryId;
                if (!DB::table('countries')->where('id', $countryId)->exists()) {
                    $this->warn("⚠️ Country {$countryId} missing for '{$team['name']}', using Unknown Country ({$unknownCountryId})");
                    $countryId = $unknownCountryId;
                }

                // ✅ Safe venue
                $venueId = $team['venue_id'] ?? $unknownVenueId;
                if (!DB::table('venues')->where('id', $venueId)->exists()) {
                    $this->warn("⚠️ Venue {$venueId} missing for '{$team['name']}', using Unknown Venue ({$unknownVenueId})");
                    $venueId = $unknownVenueId;
                }

                $model = Teams::find($team['id']) ?? new Teams();
                $model->id          = $team['id'];
                $model->is_top_team = $team['is_top_team'] ?? false;
                $model->country_id  = $countryId;
                $model->venue_id    = $venueId;
                $model->gender      = $team['gender'] ?? 'unknown';
                $model->short_code  = isset($team['short_code']) ? substr($team['short_code'], 0, 50) : null;
                $model->image_path  = $team['image_path'] ?? null;
                $model->founded     = $team['founded'] ?? null;
                $model->type        = $team['type'] ?? null;
                $model->placeholder = $team['placeholder'] ?? false;
                $model->status      = true;

                // 🌐 Multilingual names
                if ($lang === 'ar') {
                    $model->ar_name = $team['name'];
                } else {
                    $model->en_name = $team['name'];
                }

                $model->save();
                $total++;
            }

            $count = count($response['data']);
            $this->info("✅ Imported {$count} teams from page {$page}");

            // 🧭 Pagination
            $pagination  = $response['pagination'] ?? [];
            $hasMore     = $pagination['has_more'] ?? false;
            $nextPageUrl = $pagination['next_page'] ?? null;

            if (!$hasMore || empty($nextPageUrl)) {
                $this->info("🏁 No more pages after {$page}. Import complete.");
                break;
            }

            $nextPage = $this->extractPageNumber($nextPageUrl);
            if (!$nextPage) {
                $this->warn("⚠️ Could not detect next page number, stopping at page {$page}.");
                break;
            }

            $page = $nextPage;
            $this->line("➡️ Moving to page {$page}...");
            sleep(1);
        }

        $this->info("🎉 Successfully imported {$total} teams from SportMonks!");
        return Command::SUCCESS;
    }

    /**
     * Ensure Unknown Country (continent_id = 5) and Unknown Venue exist.
     */
    protected function ensureUnknownRecords(): array
    {
        $continentId = 5; // Already exists

        // 🌍 Unknown Country
        $countryId = DB::table('countries')->where('en_name', 'Unknown Country')->value('id');
        if (!$countryId) {
            $countryId = DB::table('countries')->insertGetId([
                'en_name'       => 'Unknown Country',
                'ar_name'       => 'دولة غير معروفة',
                'continent_id'  => $continentId,
                'fifa_name'     => 'UNK',
                'iso2'          => 'UN',
                'iso3'          => 'UNK',
                'latitude'      => null,
                'longitude'     => null,
                'status'        => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
            $this->info("✅ Created Unknown Country (ID {$countryId}, Continent ID {$continentId})");
        }

        // 🏟 Unknown Venue linked to Unknown Country
        $venueId = DB::table('venues')->where('name', 'Unknown Venue')->value('id');
        if (!$venueId) {
            $venueId = DB::table('venues')->insertGetId([
                'country_id'  => $countryId,
                'name'        => 'Unknown Venue',
                'city_name'   => 'Unknown City',
                'surface'     => 'unknown',
                'capacity'    => 0,
                'status'      => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
            $this->info("✅ Created Unknown Venue (ID {$venueId}) linked to Country {$countryId}");
        }

        $this->line("✅ Using Unknown Country ID {$countryId} and Venue ID {$venueId}");
        return [$countryId, $venueId];
    }

    /**
     * Fetch a page of teams from SportMonks API.
     */
    protected function getTeams(int $page, string $lang): ?array
    {
        $token = config('services.sportmonks.token');
        $url   = "https://api.sportmonks.com/v3/football/teams?api_token={$token}&locale={$lang}&page={$page}";

        $response = Http::timeout(60)->get($url);

        if ($response->failed()) {
            $this->error("❌ API request failed on page {$page}");
            return [];
        }

        return $response->json();
    }

    /**
     * Extract page number from next_page URL.
     */
    protected function extractPageNumber(?string $url): ?int
    {
        if (empty($url)) return null;
        $parts = parse_url($url);
        if (!isset($parts['query'])) return null;

        parse_str($parts['query'], $query);
        return $query['page'] ?? null;
    }
}
