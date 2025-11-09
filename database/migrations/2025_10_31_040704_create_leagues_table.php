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
        Schema::create('leagues', function (Blueprint $table) {
            $table->id();

            // Foreign key to countries table
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');

            // Basic info
            $table->string('en_name');
            $table->string('ar_name')->nullable();
            $table->string('type')->nullable(); // e.g., "domestic", "international"
            $table->string('short_code')->nullable(); // e.g., "EPL", "UCL"
            $table->string('sub_type')->nullable(); // e.g., "cup", "league"
            $table->string('category')->nullable(); // e.g., "men", "women", "youth"

            // Image path for league logo
            $table->string('image_path')->nullable();

            // Status fields
            $table->boolean('status')->default(true); // Active/inactive league
            $table->boolean('cards_status')->default(false); // For enabling cards game in this league

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leagues');
    }
};
