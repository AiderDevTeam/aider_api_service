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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('vendor_id')->index(); //changed to integer in diff migration
            $table->string('status')->index()->default('pending');
            $table->double('collection_amount', 10, 2);
            $table->string('collection_status')->index()->default('not started');
            $table->double('disbursement_amount', 10, 2)->nullable();
            $table->string('disbursement_status')->index()->default('not started');
            $table->string('reversal_status')->index()->nullable();
            $table->string('booking_acceptance_status')->default('pending')->index();
            $table->string('vendor_pickup_status')->default('pending')->index();
            $table->string('user_pickup_status')->default('pending')->index();
            $table->string('vendor_drop_off_status')->default('pending')->index();
            $table->string('user_drop_off_status')->default('pending')->index();
            $table->string('booking_number')->unique()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
