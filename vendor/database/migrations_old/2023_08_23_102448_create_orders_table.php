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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique()->nullable();
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->string('status')->default('pending');
            $table->string('collection_status')->default('pending');
            $table->string('delivery_amount');
            $table->string('discounted_amount')->nullable();
            $table->string('items_amount')->nullable();
            $table->string('destination');
            $table->string('description');
            $table->string('recipient_contact');
            $table->string('recipient_sort_code');
            $table->string('recipient_alternative_contact')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
