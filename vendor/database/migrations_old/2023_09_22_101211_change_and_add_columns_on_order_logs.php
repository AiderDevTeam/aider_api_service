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
        Schema::table('order_logs', function (Blueprint $table) {
            $table->string('order_number');
            $table->string('accepted_by')->nullable()->change();
            $table->date('accepted_date')->nullable()->change();
            $table->string('rejected_reason')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_logs', function (Blueprint $table) {
            $table->dropColumn('order_number');
            $table->string('accepted_by')->nullable()->change();
            $table->date('accepted_date')->nullable()->change();
            $table->string('rejected_reason')->nullable()->change();
        });
    }
};
