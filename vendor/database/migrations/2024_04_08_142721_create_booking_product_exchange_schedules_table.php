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
        Schema::create('booking_product_exchange_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique();
            $table->foreignId('booking_product_id');
            $table->string('city')->nullable();
            $table->string('origin_name')->nullable();
            $table->string('country')->index()->default('nigeria');
            $table->string('country_code')->index()->default('ng');
            $table->double('longitude');
            $table->double('latitude');
            $table->time('time_of_exchange');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_product_exchange_schedules');
    }
};
