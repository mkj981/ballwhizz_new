<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 🔹 Step 1: Drop existing FK safely
        Schema::table('user_cards', function (Blueprint $table) {
            if (Schema::hasColumn('user_cards', 'position_id')) {
                try {
                    $table->dropForeign(['position_id']);
                } catch (\Throwable $e) {
                    // Ignore if FK doesn't exist
                }
            }
        });

        // 🔹 Step 2: Make column nullable and re-add FK constraint
        Schema::table('user_cards', function (Blueprint $table) {
            // Ensure the column exists before changing it
            if (Schema::hasColumn('user_cards', 'position_id')) {
                // Allow NULL values
                $table->unsignedBigInteger('position_id')->nullable()->change();

                // Re-add the FK constraint
                $table->foreign('position_id')
                    ->references('id')
                    ->on('positions')
                    ->nullOnDelete(); // ⬅️ This sets position_id to NULL if position is deleted
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_cards', function (Blueprint $table) {
            try {
                $table->dropForeign(['position_id']);
            } catch (\Throwable $e) {
                // ignore if already dropped
            }

            // Restore to NOT NULL (original behavior)
            $table->unsignedBigInteger('position_id')->nullable(false)->change();

            // Re-add the old FK (cascade delete)
            $table->foreign('position_id')
                ->references('id')
                ->on('positions')
                ->cascadeOnDelete();
        });
    }
};
