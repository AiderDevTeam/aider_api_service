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
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropUnique(['account_number']);
        });

        Schema::table('wallets', function (Blueprint $table) {
            $table->unique(['account_number', 'user_id']);
        });
    }

    public function down()
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropUnique(['account_number', 'user_id']);
            $table->unique(['account_number']);
        });
    }

};
