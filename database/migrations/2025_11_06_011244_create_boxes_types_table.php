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
        Schema::create('boxes_types', function (Blueprint $table) {
            $table->id();
            $table->string('en_name');
            $table->string('ar_name');
            $table->text('en_description')->nullable();
            $table->text('ar_description')->nullable();
            $table->integer('time')->default(0); // hours or minutes to open
            $table->integer('gold_players')->default(0);
            $table->integer('silver_players')->default(0);
            $table->integer('bronze_players')->default(0);
            $table->integer('special_players')->default(0);
            $table->integer('gem')->default(0);
            $table->integer('coins')->default(0);
            $table->integer('xp')->default(0);
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('swap')->default(false);
            $table->integer('swap_power')->nullable();
            $table->integer('gem_cost')->default(0);
            $table->string('image')->nullable();
            $table->string('open_image')->nullable();
            $table->text('en_swap_trade_in_desc')->nullable();
            $table->text('ar_swap_trade_in_desc')->nullable();
            $table->text('en_swap_buy_desc')->nullable();
            $table->text('ar_swap_buy_desc')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boxes_types');
    }
};
