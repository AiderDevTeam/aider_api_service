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
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('referrer_id');
            $table->string('referred_id');
            $table->unsignedBigInteger('campaign_channel_id');
            $table->string('code')->index();
            $table->string('referral_link')->nullable();
            $table->foreign('referrer_id')->references('external_id')->on('users'); 
            $table->foreign('referred_id')->references('external_id')->on('users'); 
            $table->foreign('campaign_channel_id')->references('id')->on('campaign_channels'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
