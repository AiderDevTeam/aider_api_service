<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('referral_rewards', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique();
            $table->foreignId('referral_id');
            $table->string('reward_status')->default('pending')->index();
            $table->double('reward_value');
            $table->string('referrer_account_number')->index();
            $table->string('referrer_account_number_sort_code');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_rewards');
    }
};
