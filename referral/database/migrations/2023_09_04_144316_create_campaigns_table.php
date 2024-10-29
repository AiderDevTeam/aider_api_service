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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_type_id')->constrained('campaign_types')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('reward_split_id')->constrained('reward_splits')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('reward_type_id')->constrained('reward_types')->onDelete('cascade')->onUpdate('cascade');
            $table->string('name');
            $table->string('description');
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_types');
    }
};
