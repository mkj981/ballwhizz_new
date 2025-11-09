<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Player;

class ImportPlayersFromOldDB extends Command
{
    protected $signature = 'import:players-old';
    protected $description = 'Import players from old database to new one, keeping IDs and skipping null season_id.';

    public function handle(): int
    {
        $this->info("🚀 Starting import of players from old database...");

        $oldPlayers = DB::connection('old_mysql')->table('players')->get();
        $total = $oldPlayers->count();
        $inserted = 0;
        $skipped = 0;

        // Cache valid IDs
        $validCountries = DB::table('countries')->pluck('id')->toArray();
        $validLeagues   = DB::table('leagues')->pluck('id')->toArray();
        $validTeams     = DB::table('teams')->pluck('id')->toArray();

        foreach ($oldPlayers as $p) {
            // Skip players with no season_id
            if (empty($p->season_id)) {
                $this->warn("⏭️ Skipped Player ID {$p->id} — no season_id.");
                $skipped++;
                continue;
            }

            // Skip if already imported
            if (Player::find($p->id)) {
                $this->line("⚠️ Player ID {$p->id} already exists — skipping.");
                continue;
            }

            // ✅ Check and normalize foreign keys
            $country_id = in_array($p->country_id, $validCountries)
                ? $p->country_id
                : 2; // default to Unknown Country ID 2

            $league_id = in_array($p->league_id, $validLeagues)
                ? $p->league_id
                : null;

            $team_id = in_array($p->team_id, $validTeams)
                ? $p->team_id
                : null;

            try {
                DB::table('players')->insert([
                    'id'              => $p->id,
                    'api_id'          => $p->api_id,
                    'season_id'       => $p->season_id,
                    'country_id'      => $country_id,
                    'league_id'       => $league_id,
                    'team_id'         => $team_id,
                    'position_id'     => $p->position_id,
                    'name'            => $p->name,
                    'en_common_name'  => $p->en_common_name,
                    'ar_common_name'  => $p->ar_common_name,
                    'date_of_birth'   => $p->date_of_birth,
                    'image_path'      => $p->image_path,
                    'default_image'   => $p->default_image,
                    'open_image'      => $p->open_image,
                    'display_name'    => $p->display_name,
                    'created_at'      => $p->created_at,
                    'updated_at'      => $p->updated_at,
                ]);

                $this->info("✅ Imported Player ID {$p->id}: {$p->name}");
                $inserted++;

            } catch (\Throwable $e) {
                $this->error("❌ Failed Player ID {$p->id}: " . $e->getMessage());
            }
        }

        $this->newLine(2);
        $this->info("🎯 Import completed!");
        $this->line("✅ Inserted: {$inserted}");
        $this->line("⏭️ Skipped (no season): {$skipped}");
        $this->line("📦 Total processed: {$total}");

        return Command::SUCCESS;
    }
}
