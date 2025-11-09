<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Seasons;

class ImportSeasonTeamsFromSportmonks extends Command
{
    protected $signature = 'import:season-teams';
    protected $description = 'Import season-team relations from SportMonks and insert into season_team pivot table (skip unknown teams).';

    public function handle(): int
    {
        $token = config('services.sportmonks.token', 'esEnL4WkxrGr46O6ABq99INL1Eww4iTDYz8rtABQwapL3QX8ujNiktU4hnH0');
        $seasons = Seasons::orderBy('id', 'asc')->get();
        $totalInserted = 0;
        $totalSkipped = 0;

        $this->info("🚀 Starting season-team import from SportMonks...");
        $this->line("Total seasons to process: {$seasons->count()}");

        foreach ($seasons as $season) {
            $url = "https://api.sportmonks.com/v3/football/teams/seasons/{$season->id}?api_token={$token}";
            $this->line("📅 Fetching teams for season ID {$season->id}...");

            $response = Http::timeout(60)->get($url);

            if ($response->failed()) {
                $this->warn("⚠️ Failed to fetch teams for season {$season->id}");
                continue;
            }

            $teams = $response->json('data') ?? [];
            if (empty($teams)) {
                $this->warn("⚠️ No teams returned for season {$season->id}");
                continue;
            }

            foreach ($teams as $team) {
                $teamId = $team['id'] ?? null;
                if (!$teamId) continue;

                // ✅ Skip if team doesn't exist locally
                $exists = DB::table('teams')->where('id', $teamId)->exists();
                if (!$exists) {
                    $this->warn("⏩ Skipped team ID {$teamId} — not found in local teams table.");
                    $totalSkipped++;
                    continue;
                }

                DB::table('season_team')->updateOrInsert(
                    ['season_id' => $season->id, 'team_id' => $teamId],
                    ['created_at' => now(), 'updated_at' => now()]
                );

                $totalInserted++;
            }

            $this->info("✅ Linked " . count($teams) . " teams to season {$season->id}");
            sleep(1);
        }

        $this->info("🎉 Import complete!");
        $this->info("➡️ Total linked: {$totalInserted}");
        $this->warn("➡️ Total skipped (missing locally): {$totalSkipped}");

        return Command::SUCCESS;
    }
}
