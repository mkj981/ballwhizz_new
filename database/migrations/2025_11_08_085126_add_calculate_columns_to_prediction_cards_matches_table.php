<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prediction_cards_matches', function (Blueprint $table) {
            $table->tinyInteger('prediction_calculate')->default(0)->after('status');
            $table->tinyInteger('card_calculate')->default(0)->after('prediction_calculate');
        });
    }

    public function down(): void
    {
        Schema::table('prediction_cards_matches', function (Blueprint $table) {
            $table->dropColumn(['prediction_calculate', 'card_calculate']);
        });
    }
};
