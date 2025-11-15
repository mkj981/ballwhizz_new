<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('odds_markets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('legacy_id')->nullable();
            $table->string('name');
            $table->string('developer_name')->nullable();
            $table->boolean('has_winning_calculations')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('odds_markets');
    }
};
