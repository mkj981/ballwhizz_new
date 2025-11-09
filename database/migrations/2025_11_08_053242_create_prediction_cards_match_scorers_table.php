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
        Schema::create('prediction_cards_match_scorers', function (Blueprint $table) {
            $table->id();

            // 🔹 Reference to match (FK)
            $table->foreignId('prediction_match_id')
                ->constrained('prediction_cards_matches')
                ->onDelete('cascade');

            // 🔹 Player reference (FK)
            $table->foreignId('player_id')
                ->constrained('players')
                ->onDelete('cascade');

            // 🔹 Team side (home/away)
            $table->enum('team_side', ['home', 'away'])->comment('home or away');

            // 🔹 Goal metadata (optional)
            $table->tinyInteger('minute')->nullable()->comment('Minute of goal if available');
            $table->string('type')->nullable()->comment('e.g., penalty, own goal, header, etc.');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prediction_cards_match_scorers');
    }
};
