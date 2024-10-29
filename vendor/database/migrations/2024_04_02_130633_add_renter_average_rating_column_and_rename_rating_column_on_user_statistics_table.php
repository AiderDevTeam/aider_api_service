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
            $table->renameColumn('rating', 'vendor_average_rating');
            $table->decimal('renter_average_rating', 5, 1)->default(0)->index()->after('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_statistics', function (Blueprint $table) {
            $table->renameColumn('vendor_average_rating', 'rating');
            $table->dropColumn('renter_average_rating');
        });
    }
};
