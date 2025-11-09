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
        Schema::create('week_months', function (Blueprint $table) {
            $table->id();
            $table->string('week_name')->comment('e.g. Week 1, Week 25, or August Week 2');
            $table->integer('week')->comment('Numeric week number');
            $table->dateTime('start_date')->comment('Always starts at 00:00:00');
            $table->dateTime('end_date')->comment('Always ends at 23:59:00');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('week_months');
    }
};
