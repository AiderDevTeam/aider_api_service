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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('disbursement_status')->default('pending');
            $table->double('disbursement_amount');
            $table->double('payout_commission');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('disbursement_status');
            $table->dropColumn('disbursement_amount');
            $table->dropColumn('payout_commission');
        });
    }
};
