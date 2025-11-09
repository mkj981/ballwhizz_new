<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Common fields
            $table->string('name')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('password')->nullable();

            // Mobile app fields
            $table->string('uid')->nullable()->comment('Social provider user ID');
            $table->string('social_type')->nullable()->comment('google, apple, facebook, etc');
            $table->string('mobile')->nullable();
            $table->string('country_code')->nullable();
            $table->string('team')->nullable();
            $table->string('FCM_token')->nullable();
            $table->tinyInteger('lang')->default(1)->comment('1=en, 2=ar');
            $table->string('referral_code')->nullable();
            $table->string('comefrom')->nullable();

            // Game currency defaults
            $table->unsignedBigInteger('coins')->default(0);
            $table->unsignedBigInteger('gem')->default(0);
            $table->unsignedBigInteger('xp')->default(0);

            // Admin-related fields
            $table->string('role')->default('user')->comment('user, admin, super_admin');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
