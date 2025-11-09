<?php

namespace App\Http\Controllers\Manual;

use App\Http\Controllers\Controller;
use App\Livewire\Admin\SeasonTeams;
use App\Models\Player;
use App\Models\Seasons;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class TestController extends Controller
{
    public function index(Request $request)
    {
        $leagueId = 8; // or $request->get('league_id');
        $token = 'esEnL4WkxrGr46O6ABq99INL1Eww4iTDYz8rtABQwapL3QX8ujNiktU4hnH0';
        $lang = 'en';

        $current_season = Seasons::where('league_id', $leagueId)
            ->where('is_current', 1)
            ->first();

        if (!$current_season) {
            return response()->json(['error' => 'No current season found for this league'], 404);
        }

        $league_players = [];
        $seasonTeams = DB::table('season_team')
            ->where('season_id', $current_season->id)
            ->get();

        foreach ($seasonTeams as $seasonTeam) {
            $playersResponse = $this->getTeamPlayers($seasonTeam->team_id, $lang, $token);
            $players = $playersResponse['data']['players'] ?? [];

            foreach ($players as $player) {
                $player_id = $player['player']['id'] ?? null;
                if ($player_id) {
                    $league_players[] = $player_id;
                }
            }
        }

        // ✅ Delete players not in API list and return their details
        if (!empty($league_players)) {
            $toDelete = Player::where('league_id', $leagueId)
                ->whereNotIn('api_id', $league_players)
                ->get(['id', 'name', 'api_id']);

            $deletedPlayers = $toDelete->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'api_id' => $p->api_id,
                ];
            })->toArray();

            $deletedCount = count($deletedPlayers);

            // Perform the deletion
            Player::whereIn('id', $toDelete->pluck('id'))->delete();

            return response()->json([
                'status' => 'success',
                'message' => "🗑️ Deleted {$deletedCount} outdated players from league {$leagueId}.",
                'deleted_players' => $deletedPlayers,
                'kept_players_count' => count($league_players),
            ]);
        } else {
            return response()->json([
                'status' => 'warning',
                'message' => 'No players fetched from API — skipping deletion.',
            ]);
        }
    }

    private function getTeamPlayers($team_id, $lang, $token)
    {
        $url = "https://api.sportmonks.com/v3/football/teams/{$team_id}?api_token={$token}&locale={$lang}&include=players.player";
        $response = Http::timeout(90)->get($url);
        return $response->json() ?? [];
    }
}
