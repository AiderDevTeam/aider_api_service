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
            $table->mediumInteger('vendor_reviews_count')->default(0)->after('renter_average_rating');
            $table->mediumInteger('renter_reviews_count')->default(0)->after('vendor_reviews_count');
            $table->json('renter_individual_rating_counts')->nullable()->after('individual_rating_counts');
            $table->renameColumn('individual_rating_counts', 'vendor_individual_rating_counts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_statistics', function (Blueprint $table) {
            $table->dropColumn('vendor_reviews_count');
            $table->dropColumn('renter_reviews_count');
            $table->dropColumn('renter_individual_rating_counts');
            $table->renameColumn('vendor_individual_rating_counts', 'individual_rating_counts');
        });
    }
};
