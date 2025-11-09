<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seasons', function (Blueprint $table) {
            $table->id();

            // 🔗 Relations
            $table->foreignId('league_id')->constrained('leagues')->onDelete('cascade');
            $table->unsignedBigInteger('tie_breaker_rule_id')->nullable();

            // 🏆 Season details
            $table->string('name')->nullable();
            $table->boolean('finished')->default(false);
            $table->boolean('pending')->default(false);
            $table->boolean('is_current')->default(false);

            // 📅 Important dates
            $table->dateTime('starting_at')->nullable();
            $table->dateTime('ending_at')->nullable();
            $table->dateTime('standings_recalculated_at')->nullable();

            // ⚙️ Status
            $table->boolean('status')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seasons');
    }
};
