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
        Schema::create('continents', function (Blueprint $table) {
            $table->id();
            $table->string('code', 5)->unique(); // example: EU, AS, AF
            $table->string('en_name');
            $table->string('ar_name')->nullable();
            $table->string('dark_img')->nullable();   // path or filename for dark theme
            $table->string('light_img')->nullable();  // path or filename for light theme
            $table->boolean('status')->default(true); // active/inactive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('continents');
    }
};
