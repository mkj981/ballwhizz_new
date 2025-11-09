<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('api_id')->nullable()->index();
            $table->unsignedBigInteger('season_id')->nullable()->index();
            $table->unsignedBigInteger('country_id')->nullable()->index();
            $table->unsignedBigInteger('league_id')->nullable()->index();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->unsignedBigInteger('position_id')->nullable()->index();

            $table->string('name')->nullable();
            $table->string('en_common_name')->nullable();
            $table->string('ar_common_name')->nullable();
            $table->date('date_of_birth')->nullable();

            $table->string('image_path')->nullable();
            $table->string('default_image')->nullable();
            $table->string('open_image')->nullable();
            $table->string('display_name')->nullable();

            $table->timestamps();

            // Optional foreign keys (safe even if not enforced yet)
            $table->foreign('season_id')->references('id')->on('seasons')->onDelete('set null');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
            $table->foreign('league_id')->references('id')->on('leagues')->onDelete('set null');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
