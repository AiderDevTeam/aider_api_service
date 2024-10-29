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
        Schema::create('delivery_processors', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique();
            $table->string('name')->unique();
            $table->boolean('active')->default(false);
            $table->boolean('express')->default(false);
            $table->boolean('next_day')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_processors');
    }
};
