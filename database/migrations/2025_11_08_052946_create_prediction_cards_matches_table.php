<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('prediction_cards_matches', function (Blueprint $table) {
            $table->id();

            // 🔹 Foreign key to leagues
            $table->foreignId('league_id')
                ->nullable()
                ->constrained('leagues')
                ->onDelete('set null');

            // 🔹 External match reference (not FK)
            $table->unsignedBigInteger('match_id')
                ->nullable()
                ->comment('External match reference (not FK)');

            // 🔹 Foreign keys to teams
            $table->foreignId('home_team_id')
                ->constrained('teams')
                ->onDelete('cascade');

            $table->foreignId('away_team_id')
                ->constrained('teams')
                ->onDelete('cascade');

            // 🔹 Results
            $table->integer('home_team_result')->nullable();
            $table->integer('away_team_result')->nullable();

            // 🔹 Status (pending/finished)
            $table->tinyInteger('status')
                ->default(0)
                ->comment('0 = pending, 1 = finished');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prediction_cards_matches');
    }
};
