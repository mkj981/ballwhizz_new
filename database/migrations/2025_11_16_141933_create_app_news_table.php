<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('appnews', function (Blueprint $table) {
            $table->id();

            $table->text('short_text_en')->nullable();
            $table->text('short_text_ar')->nullable();

            $table->longText('long_text_en')->nullable();
            $table->longText('long_text_ar')->nullable();

            $table->string('video_url')->nullable();

            // JSON field for multiple image paths
            $table->json('images')->nullable();

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_news');
    }
};
