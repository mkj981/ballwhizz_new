<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users_rankings', function (Blueprint $table) {
            $table->unsignedBigInteger('home_team_id')->nullable()->after('team_id');
            $table->unsignedBigInteger('away_team_id')->nullable()->after('home_team_id');

            $table->integer('home_prediction')->nullable()->after('away_team_id');
            $table->integer('away_prediction')->nullable()->after('home_prediction');
        });
    }

    public function down(): void
    {
        Schema::table('users_rankings', function (Blueprint $table) {
            $table->dropColumn([
                'home_team_id',
                'away_team_id',
                'home_prediction',
                'away_prediction',
            ]);
        });
    }
};
