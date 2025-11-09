<?php

namespace App\Console\Commands;

use App\Models\Player;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Seasons;
use App\Models\Players;
use App\Models\PlayersCards;

class ImportPlayersFromSportmonks extends Command
{
    protected $signature = 'import:players-sportmonks {league_id?} {lang=en}';
    protected $description = 'Import all players for current seasons using SportMonks API (update, insert, and cleanup missing ones)';

    public function handle(): int
    {
        $league_id = $this->argument('league_id');
        $lang = $this->argument('lang');
        $token = config('services.sportmonks.token')
            ?? env('SPORTMONKS_API_TOKEN', 'esEnL4WkxrGr46O6ABq99INL1Eww4iTDYz8rtABQwapL3QX8ujNiktU4hnH0');

        set_time_limit(0);
        $this->info("🚀 Starting player import from SportMonks for league: " . ($league_id ?? 'ALL'));

        $seasonQuery = Seasons::where('is_current', 1);
        if ($league_id) {
            $seasonQuery->where('league_id', $league_id);
        }

        $seasonIds = $seasonQuery->pluck('id');
        if ($seasonIds->isEmpty()) {
            $this->error("❌ No current seasons found.");
            return Command::FAILURE;
        }

        foreach ($seasonIds as $season_id) {
            $this->line("📅 Processing Season ID: {$season_id}");

            $teamsResponse = $this->getSeasonTeams($season_id, $lang, $token);
            $teams = $teamsResponse['data'] ?? [];

            if (empty($teams)) {
                $this->warn("⚠️ No teams found for Season {$season_id}");
                continue;
            }

            $allApiIdsForThisLeague = []; // 🧩 Collect all valid API player IDs for cleanup

            foreach ($teams as $team) {
                $team_id = $team['id'] ?? null;
                if (!$team_id) continue;

                $this->line("⚽ Processing Team ID: {$team_id}");

                $playersResponse = $this->getTeamPlayers($team_id, $lang, $token);
                $playersList = $playersResponse['data']['players'] ?? [];

                if (empty($playersList)) {
                    $this->warn("⚠️ No players found for Team ID {$team_id}");
                    continue;
                }

                foreach ($playersList as $playerData) {
                    $api_id = $playerData['player_id'] ?? ($playerData['player']['id'] ?? null);
                    if (!$api_id) continue;

                    $allApiIdsForThisLeague[] = $api_id; // ✅ add for cleanup later

                    $player = $playerData['player'] ?? [];
                    $position_id = $player['detailed_position_id'] ?? ($player['position_id'] ?? 0);

                    $existing = Player::where('api_id', $api_id)->first();

                    if ($existing) {
                        // 🔁 Update team, league, or season if changed
                        $updateData = [];

                        if ($existing->team_id !== $team_id) {
                            $updateData['team_id'] = $team_id;
                        }
                        if ($existing->league_id !== $league_id) {
                            $updateData['league_id'] = $league_id;
                        }
                        if ($existing->season_id !== $season_id) {
                            $updateData['season_id'] = $season_id;
                        }

                        if (!empty($updateData)) {
                            $existing->update($updateData);
                            $this->line("🔁 Updated player {$existing->name} (ID {$existing->id})");
                        }

                    } else {
                        // 🆕 Create new player
                        $createPayload = [
                            'api_id'         => $api_id,
                            'league_id'      => $league_id,
                            'team_id'        => $team_id,
                            'season_id'      => $season_id,
                            'position_id'    => $position_id,
                            'country_id'     => $player['country_id'] ?? 2,
                            'name'           => $player['name'] ?? null,
                            'en_common_name' => $player['common_name'] ?? null,
                            'ar_common_name' => $player['common_name'] ?? null,
                            'display_name'   => $player['display_name'] ?? null,
                            'date_of_birth'  => $player['date_of_birth'] ?? null,
                            'image_path'     => $player['image_path'] ?? null,
                            'open_image'     => $player['open_image'] ?? null,
                        ];

                        $newPlayer = Player::create($createPayload);
                        $this->info("✅ Created new player: {$newPlayer->name}");
                    }
                }
            }

            // 🧹 Final cleanup: delete players for this league not in new list
            if (!empty($allApiIdsForThisLeague)) {
                $deletedCount = Player::where('league_id', $league_id)
                    ->whereNotIn('api_id', $allApiIdsForThisLeague)
                    ->delete();

                if ($deletedCount > 0) {
                    $this->warn("🗑️ Deleted {$deletedCount} outdated players from league {$league_id}");
                } else {
                    $this->line("✅ No outdated players found for league {$league_id}");
                }
            }
        }


        $this->info("🎯 Player import completed successfully!");
        return Command::SUCCESS;
    }

    /**
     * Fetch teams in a season.
     */
    private function getSeasonTeams($season_id, $lang, $token)
    {
        $url = "https://api.sportmonks.com/v3/football/teams/seasons/{$season_id}?api_token={$token}&locale={$lang}";
        $response = Http::timeout(90)->get($url);
        return $response->json() ?? [];
    }

    /**
     * Fetch team players.
     */
    private function getTeamPlayers($team_id, $lang, $token)
    {
        $url = "https://api.sportmonks.com/v3/football/teams/{$team_id}?api_token={$token}&locale={$lang}&include=players.player";
        $response = Http::timeout(90)->get($url);
        return $response->json() ?? [];
    }
}
