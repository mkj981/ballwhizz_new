<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('en_name');
            $table->string('ar_name');

            // 🔗 Foreign key to continents table
            $table->foreignId('continent_id')
                ->constrained('continents')
                ->onDelete('cascade');

            $table->string('fifa_name')->nullable();
            $table->string('iso2')->nullable();
            $table->string('iso3')->nullable();

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->text('borders')->nullable(); // can store comma-separated or JSON of neighbor countries
            $table->string('image_path')->nullable();
            $table->boolean('status')->default(1); // 1 = active, 0 = inactive

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
