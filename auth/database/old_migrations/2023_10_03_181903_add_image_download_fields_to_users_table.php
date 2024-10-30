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
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_photo_url_b')->nullable();
            $table->string('profile_photo_url_status')->default('false');
        });

      
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_photos', function (Blueprint $table) {
            //
            $table->dropColumn(['photoUrl_b', 'photoUrl_status']);
        });

        Schema::table('vendors', function (Blueprint $table) {
            //
            $table->dropColumn(['shop_logo_url_b', 'shop_logo_url_b_status']);
        });
    }
};