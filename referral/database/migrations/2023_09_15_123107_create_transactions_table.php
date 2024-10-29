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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('stan')->index();
            $table->string('referrer_id');
            $table->string('referred_id');
            $table->unsignedBigInteger('campaign_id');
            $table->decimal('amount', 9, 2);
            $table->string('response_code');
            $table->string('response_message');
            $table->text('full_request');
            $table->text('full_response');
            $table->enum('has_performed_transaction', ['yes', 'no'])->default('no');
            $table->foreign('referrer_id')->references('external_id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('referred_id')->references('external_id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('cascade')->onUpdate('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
