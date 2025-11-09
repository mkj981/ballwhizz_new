<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            // 🔧 Change hashtags from string to text
            $table->text('hashtags')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            // 🔙 Revert back to string if needed
            $table->string('hashtags')->nullable()->change();
        });
    }
};
