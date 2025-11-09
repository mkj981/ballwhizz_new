<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\PredictionCardsMatches;
use App\Models\Leagues;
use App\Models\Teams;
use Carbon\Carbon;

class ImportPredictionMatchesFromSportmonks extends Command
{
    protected $signature = 'import:prediction-matches-sportmonks {--start_date=} {--end_date=} {--lang=en}';
    protected $description = 'Import prediction card matches from the SportMonks API between given dates.';

    public function handle(): int
    {
        $start = $this->option('start_date');
        $end   = $this->option('end_date');
        $lang  = $this->option('lang') ?? 'en';

        if (!$start || !$end) {
            $this->error('❌ Please provide --start_date and --end_date options.');
            return Command::FAILURE;
        }

        $token = env('SPORTMONKS_API_TOKEN');
        $this->info("⚽ Importing matches from SportMonks between {$start} and {$end}...");

        $page = 1;
        $imported = 0;
        $skipped = 0;

        while (true) {
            $url = "https://api.sportmonks.com/v3/football/fixtures/between/{$start}/{$end}"
                . "?api_token={$token}"
                . "&locale={$lang}"
                . "&include=participants"
                . "&per_page=50&page={$page}";

            try {
                $response = Http::timeout(40)->get($url);
            } catch (\Throwable $e) {
                $this->error("❌ HTTP error: " . $e->getMessage());
                Log::error("HTTP error on page {$page}", ['error' => $e->getMessage()]);
                break;
            }

            if ($response->failed()) {
                $this->error('❌ API request failed: ' . $response->status());
                break;
            }

            $body = $response->json();
            if (empty($body['data'])) {
                $this->warn("⚠️ No data found on page {$page}.");
                break;
            }

            foreach ($body['data'] as $fixture) {
                try {
                    $matchId = $fixture['id'] ?? null;
                    $leagueIdFromApi = $fixture['league_id'] ?? null;
                    $matchName = $fixture['name'] ?? 'Unknown Match';
                    $startingAtRaw = $fixture['starting_at'] ?? null;

                    // 🕒 Convert to proper datetime format
                    $startingAt = null;
                    if (!empty($startingAtRaw)) {
                        try {
                            $startingAt = Carbon::parse($startingAtRaw)->format('Y-m-d H:i:s');
                        } catch (\Throwable $e) {
                            Log::warning("⚠️ Invalid starting_at format", ['raw' => $startingAtRaw]);
                        }
                    }

                    $this->line("🎯 {$matchName} (League ID: {$leagueIdFromApi}) | 🕒 {$startingAt}");

                    if (!$matchId || !$leagueIdFromApi) {
                        $this->warn("⚠️ Missing match_id or league_id for {$matchName}");
                        $skipped++;
                        continue;
                    }

                    // ✅ Check league
                    $league = Leagues::find($leagueIdFromApi);
                    if (!$league) {
                        $this->warn("⛔ League not found in DB with ID {$leagueIdFromApi}");
                        $skipped++;
                        continue;
                    }

                    if ((int)$league->status !== 1) {
                        $this->warn("⏩ League inactive: {$league->en_name}");
                        $skipped++;
                        continue;
                    }

                    // 🏟 Detect home & away IDs
                    $homeTeamApiId = null;
                    $awayTeamApiId = null;

                    if (!empty($fixture['participants'])) {
                        foreach ($fixture['participants'] as $team) {
                            $location = $team['meta']['location'] ?? null;
                            if ($location === 'home') {
                                $homeTeamApiId = $team['id'];
                            } elseif ($location === 'away') {
                                $awayTeamApiId = $team['id'];
                            }
                        }
                    }

                    if (!$homeTeamApiId || !$awayTeamApiId) {
                        $this->warn("⚠️ Couldn’t detect home/away IDs for {$matchName}");
                        $skipped++;
                        continue;
                    }

                    // ✅ Find local teams
                    $homeTeam = Teams::find($homeTeamApiId);
                    $awayTeam = Teams::find($awayTeamApiId);

                    if (!$homeTeam || !$awayTeam) {
                        $this->warn("⚠️ Missing team(s) in DB for {$matchName}");
                        $skipped++;
                        continue;
                    }

                    // 🏁 Determine match status
                    $statusText = strtolower($fixture['result_info'] ?? '');
                    $finished = (str_contains($statusText, 'won') || str_contains($statusText, 'ended')) ? 1 : 0;

                    // ✅ Save or update record
                    PredictionCardsMatches::updateOrCreate(
                        ['match_id' => $matchId],
                        [
                            'league_id'        => $league->id,
                            'home_team_id'     => $homeTeam->id,
                            'away_team_id'     => $awayTeam->id,
                            'starting_at'      => $startingAt, // ✅ Save properly formatted
                            'home_team_result' => null,
                            'away_team_result' => null,
                            'status'           => $finished,
                        ]
                    );

                    $this->info("✅ Saved: {$matchName} ({$league->en_name}) — starts {$startingAt}");
                    $imported++;

                } catch (\Throwable $e) {
                    $this->error("❌ Error for fixture {$fixture['id']}: {$e->getMessage()}");
                    Log::error("Error saving fixture", [
                        'fixture_id' => $fixture['id'] ?? null,
                        'error' => $e->getMessage(),
                    ]);
                    $skipped++;
                }
            }

            // 🔁 Pagination
            if (!empty($body['pagination']['has_more']) && $body['pagination']['has_more'] === true) {
                $page++;
                sleep(1);
            } else {
                break;
            }
        }

        $this->info("✅ Imported {$imported} matches. Skipped {$skipped}.");
        return Command::SUCCESS;
    }
}
