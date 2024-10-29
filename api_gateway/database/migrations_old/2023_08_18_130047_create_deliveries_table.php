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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique();
            $table->morphs('delivery_processor');
            $table->string('status')->index();
            $table->string('tracking_number')->index()->nullable();
            $table->string('service_webhook');
            $table->string('currency');
            $table->string('delivery_option')->index();
            $table->boolean('is_pickup')->default(true);
            $table->boolean('is_fulfillment_delivery')->default(false);
            $table->double('amount_to_collect')->nullable();
            $table->string('service_type')->default('intracity');
            $table->boolean('is_prepaid_delivery')->default(true);
            $table->dateTime('pick_up_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
