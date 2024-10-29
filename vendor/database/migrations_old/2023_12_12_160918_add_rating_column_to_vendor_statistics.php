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
        Schema::table('vendor_statistics', function (Blueprint $table) {
            $table->decimal('rating', 5, 1)->default(0);
            $table->json('individual_rating_counts')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_statistics', function (Blueprint $table) {
            $table->dropColumn('rating');
            $table->dropColumn('individual_rating_counts');
        });
    }
};
