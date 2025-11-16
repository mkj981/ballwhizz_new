<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('users_rankings', function (Blueprint $table) {
            $table->string('position_in_app')->nullable()->after('position');
        });
    }

    public function down()
    {
        Schema::table('users_rankings', function (Blueprint $table) {
            $table->dropColumn('position_in_app');
        });
    }
};
