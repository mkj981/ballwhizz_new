<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users_rankings', function (Blueprint $table) {
            $table->id();

            // 🔹 Core identifiers
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('match_id')->comment('Match identifier (not FK)');
            $table->foreignId('league_id')->nullable()->constrained('leagues')->nullOnDelete();
            $table->enum('type', ['cards', 'trivia', 'prediction'])->comment('Ranking category');

            // 🔹 Optional relations
            $table->unsignedBigInteger('player_id')->nullable()->comment('Player identifier if applicable');
            $table->foreignId('team_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->foreignId('card_id')->nullable()->constrained('players_cards')->nullOnDelete();

            // 🔹 Unified points
            $table->integer('points')->default(0)->comment('Total points earned by the user for this entry');

            // 🔹 Scorer info
            $table->longText('scorer_list')->nullable()->comment('Stores scorer names or JSON list');

            // 🔹 Match results
            $table->integer('home_team_result')->nullable()->comment('Goals/result for home team');
            $table->integer('away_team_result')->nullable()->comment('Goals/result for away team');

            // 🔹 Week linkage (for Cardz & Prediction)
            $table->foreignId('cards_week_id')->nullable()->constrained('cards_weeks')->nullOnDelete();
            $table->foreignId('prediction_week_id')->nullable()->constrained('week_months')->nullOnDelete();

            // 🔹 Extra details
            $table->boolean('is_sub')->nullable()->comment('Indicates if player was a substitute');
            $table->integer('position')->nullable()->comment('Position or ranking order');

            // 🔹 Dates
            $table->dateTime('game_date')->nullable()->comment('Official match date');
            $table->dateTime('game_user_date')->nullable()->comment('Date/time when user played or recorded entry');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users_rankings');
    }
};
