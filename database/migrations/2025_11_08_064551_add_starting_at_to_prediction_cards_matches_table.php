<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('prediction_cards_matches', function (Blueprint $table) {
            $table->dateTime('starting_at')->nullable()->after('away_team_id')
                ->comment('Kickoff datetime of the match');
        });
    }

    public function down(): void
    {
        Schema::table('prediction_cards_matches', function (Blueprint $table) {
            $table->dropColumn('starting_at');
        });
    }
};
