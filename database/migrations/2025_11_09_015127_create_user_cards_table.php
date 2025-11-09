<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_cards', function (Blueprint $table) {
            $table->id();

            // 🔗 Foreign Keys
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('card_id')
                ->constrained('players_cards')
                ->cascadeOnDelete();

            $table->foreignId('league_id')
                ->constrained('leagues')
                ->cascadeOnDelete();

            // ✅ position_id is optional, safe default = NULL
            $table->foreignId('position_id')
                ->nullable()
                ->default(null)
                ->constrained('positions')
                ->nullOnDelete();

            // ⚙️ Flags and position order
            $table->boolean('is_in_team')
                ->default(false)
                ->comment('Whether card is in active team');

            $table->boolean('is_sub')
                ->default(false)
                ->comment('Whether card is a substitute');

            $table->integer('in_stad')
                ->default(0)
                ->comment('Position order in stadium');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_cards');
    }
};
