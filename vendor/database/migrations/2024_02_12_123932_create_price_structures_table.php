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
        Schema::create('price_structures', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique();
            $table->string('name')->unique();
            $table->string('description');
            $table->integer('start_day')->index();
            $table->integer('end_day')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};
