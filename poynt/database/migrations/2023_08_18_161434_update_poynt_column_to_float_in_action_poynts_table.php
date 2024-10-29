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
        Schema::table('action_poynts', function (Blueprint $table) {
            $table->float('poynt')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('float_in_action_poynts', function (Blueprint $table) {
            $table->integer('poynt')->default(0)->change();
        });
    }
};
