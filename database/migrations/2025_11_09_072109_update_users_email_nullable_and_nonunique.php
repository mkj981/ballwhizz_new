<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 🧹 Drop unique index on email if it exists
            try {
                $table->dropUnique(['email']);
            } catch (\Throwable $e) {
                // silently ignore if already dropped
            }

            // ✅ Modify email column to allow duplicates and be nullable
            $table->string('email')->nullable()->change();
        });

        // Double check in case index name differs (Laravel usually uses this)
        try {
            DB::statement('ALTER TABLE users DROP INDEX users_email_unique;');
        } catch (\Throwable $e) {
            // ignore
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->unique()->nullable(false)->change();
        });
    }
};
