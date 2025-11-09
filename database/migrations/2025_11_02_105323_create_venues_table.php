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
        Schema::create('venues', function (Blueprint $table) {
            $table->id();                                // matches "id"
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->unsignedBigInteger('city_id')->nullable();  // from API
            $table->string('name');                      // "name"
            $table->string('address')->nullable();       // "address"
            $table->string('zipcode')->nullable();       // "zipcode"
            $table->string('latitude')->nullable();      // "latitude"
            $table->string('longitude')->nullable();     // "longitude"
            $table->integer('capacity')->nullable();     // "capacity"
            $table->string('image_path')->nullable();    // "image_path"
            $table->string('city_name')->nullable();     // "city_name"
            $table->string('surface')->nullable();       // "surface"
            $table->boolean('national_team')->default(false); // "national_team"
            $table->boolean('status')->default(true);    // local control flag
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};
