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
        Schema::create('stations', function (Blueprint $table) {
            $table->id(); // SportMonks station ID
            $table->string('name');
            $table->string('url')->nullable();
            $table->string('image_path')->nullable();
            $table->string('type')->nullable(); // tv, channel, stream, etc.
            $table->unsignedBigInteger('related_id')->nullable(); // related station
            $table->boolean('status')->default(true); // for admin activation control
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stations');
    }
};
