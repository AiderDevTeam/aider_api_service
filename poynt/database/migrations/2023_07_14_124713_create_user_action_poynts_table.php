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
        Schema::create('user_action_poynts', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->index();
            $table->string('user_id')->index();
            $table->string('action_poynt_id')->index()->nullable();
            $table->integer('poynt')->default(0);
            $table->double('action_value')->default(0);
            $table->enum('type', ['credit', 'debit']);
            $table->json('action_response_payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_action_poynts');
    }
};
