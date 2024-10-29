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
        Schema::table('user_statistics', function (Blueprint $table) {
            $table->unsignedInteger('vendor_bookings_pending_acceptance_count')->after('renter_bookings_pending_pickup_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_statistics', function (Blueprint $table) {
            $table->dropColumn('vendor_bookings_pending_acceptance_count');
        });
    }
};
