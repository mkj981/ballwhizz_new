<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('en_title');
            $table->string('ar_title');
            $table->text('en_text')->nullable();
            $table->text('ar_text')->nullable();
            $table->string('image')->nullable();
            $table->string('video')->nullable();

            $table->string('en_short_desc')->nullable();
            $table->string('ar_short_desc')->nullable();
            $table->string('hashtags')->nullable();

            $table->string('en_meta_title')->nullable();
            $table->string('ar_meta_title')->nullable();
            $table->text('en_meta_description')->nullable();
            $table->text('ar_meta_description')->nullable();

            $table->float('average_rating', 3, 2)->default(0); // 0.00 to 5.00
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
