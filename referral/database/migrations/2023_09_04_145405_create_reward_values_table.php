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
        Schema::create('reward_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campaign_id');
            $table->unsignedBigInteger('reward_type_id');
            $table->decimal('amount', 8, 2)->default('0');
            $table->string('point')->nullable();
            $table->foreign('campaign_id')->references('id')->on('campaigns');
            $table->foreign('reward_type_id')->references('id')->on('reward_types');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reward_values');
    }
};
