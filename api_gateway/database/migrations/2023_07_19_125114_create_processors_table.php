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
        Schema::create('processors', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique();
            $table->string('name')->unique();
            $table->boolean('active')->default(false)->index();
            $table->boolean('collect')->default(false)->index();
            $table->boolean('disburse')->default(false)->index();
            $table->boolean('direct_debit')->default(false)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('processors');
    }
};
