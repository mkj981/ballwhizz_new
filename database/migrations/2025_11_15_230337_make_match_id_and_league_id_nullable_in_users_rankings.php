<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('users_rankings', function (Blueprint $table) {
            $table->unsignedBigInteger('match_id')->nullable()->change();
            $table->unsignedBigInteger('league_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('users_rankings', function (Blueprint $table) {
            $table->unsignedBigInteger('match_id')->nullable(false)->change();
            $table->unsignedBigInteger('league_id')->nullable(false)->change();
        });
    }
};
