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
        Schema::table('carts', function (Blueprint $table) {
            $table->string('order_id')->nullable();
            $table->string('unit_price')->nullable();
            $table->string('discounted_amount')->nullable();
            $table->string('discounted_method')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn('order_id');
            $table->dropColumn('unit_price');
            $table->dropColumn('discounted_amount');
            $table->dropColumn('discounted_method');
        });
    }
};
