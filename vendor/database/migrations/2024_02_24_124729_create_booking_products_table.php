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
        Schema::create('booking_products', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id');
            $table->double('product_amount', 10, 2);
            $table->integer('product_quantity')->default(1);
            $table->double('product_value', 10, 2);
            $table->date('start_date')->index();
            $table->date('end_date')->index();
            $table->integer('booking_duration'); //in days
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_products');
    }
};
