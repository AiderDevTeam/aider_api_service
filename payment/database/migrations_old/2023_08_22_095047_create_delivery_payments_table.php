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
        Schema::create('delivery_payments', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('description')->nullable()->index();
            $table->string('delivery_external_id')->unique()->index();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_payments');
    }
};
