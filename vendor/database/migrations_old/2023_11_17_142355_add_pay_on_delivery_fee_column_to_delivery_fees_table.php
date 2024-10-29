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
        Schema::table('delivery_fees', function (Blueprint $table) {
            $table->double('pay_on_delivery_fee')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_fees', function (Blueprint $table) {
            $table->dropColumn('pay_on_delivery_fee');
        });
    }
};
