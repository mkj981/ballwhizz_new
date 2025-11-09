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
        Schema::create('players_cards', function (Blueprint $table) {
            $table->id();

            // 🔗 Foreign keys
            $table->unsignedBigInteger('player_id');
            $table->unsignedBigInteger('type_id');

            // 🔋 Default energy 10
            $table->integer('energy')->default(10);

            // 🕓 Optional fields
            $table->unsignedBigInteger('week_id')->nullable();
            $table->json('stats')->nullable();

            $table->timestamps();

            // ⚙️ Foreign key constraints (optional but recommended)
            $table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');
            $table->foreign('type_id')->references('id')->on('card_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players_cards');
    }
};
