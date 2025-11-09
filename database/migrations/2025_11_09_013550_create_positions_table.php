<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('positions', function (Blueprint $table) {
            $table->id();

            // 🟢 Basic Fields
            $table->string('code', 10)->unique()->comment('Short code like GK, CB, CM, ST');
            $table->string('en_name', 50)->comment('Position name in English');
            $table->string('ar_name', 50)->nullable()->comment('Position name in Arabic');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
