<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cards_weeks', function (Blueprint $table) {
            $table->id();

            // 🔗 Foreign keys
            $table->unsignedBigInteger('week_months_id');
            $table->unsignedBigInteger('league_id');

            // 📆 Week structure
            $table->integer('matchday')->nullable();
            $table->dateTime('start')->nullable();
            $table->dateTime('end')->nullable();
            $table->dateTime('close_at')->nullable();

            // ⚙️ Status flags
            $table->boolean('is_active')->default(0);
            $table->boolean('is_open')->default(0);

            $table->timestamps();

            // 🔒 Foreign key constraints
            $table->foreign('week_months_id')
                ->references('id')->on('week_months')
                ->onDelete('cascade');

            $table->foreign('league_id')
                ->references('id')->on('leagues')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cards_weeks');
    }
};
