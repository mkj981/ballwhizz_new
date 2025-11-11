<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateUserCardPositions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * You’ll run it as: php artisan app:update-user-card-positions
     */
    protected $signature = 'app:update-user-card-positions';

    /**
     * The console command description.
     */
    protected $description = '🔄 Update user_cards.position_id using players_cards → players relationships';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Starting update of user_cards.position_id...');

        // Perform the joined update
        $updated = DB::table('user_cards')
            ->join('players_cards', 'user_cards.card_id', '=', 'players_cards.id')
            ->join('players', 'players_cards.player_id', '=', 'players.id')
            ->join('positions', 'players.position_id', '=', 'positions.id') // ✅ ensures valid position
            ->update([
                'user_cards.position_id' => DB::raw('players.position_id')
            ]);


        $this->info("✅ Successfully updated {$updated} records in user_cards table.");

        return Command::SUCCESS;
    }
}
