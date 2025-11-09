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
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_top_team')->default(false);
            $table->unsignedBigInteger('country_id')->nullable()->index();
            $table->unsignedBigInteger('venue_id')->nullable()->index();
            $table->enum('gender', ['male', 'female', 'neutral'])->default('male');

            $table->string('en_name')->nullable();
            $table->string('ar_name')->nullable();
            $table->string('short_code', 10)->nullable();
            $table->string('image_path')->nullable();
            $table->string('founded', 10)->nullable();
            $table->string('type')->nullable();
            $table->boolean('placeholder')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();

            // ✅ Relationships (safe + clean)
            $table->foreign('country_id')
                ->references('id')
                ->on('countries')
                ->nullOnDelete();

            $table->foreign('venue_id')
                ->references('id')
                ->on('venues')
                ->nullOnDelete();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
