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
        Schema::create('delivery_fees', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique();
            $table->string('processor');
            $table->string('delivery_option');
            $table->double('fee');
            $table->double('margin');
            $table->timestamps();

            $table->unique(['processor', 'delivery_option']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_fees');
    }
};
