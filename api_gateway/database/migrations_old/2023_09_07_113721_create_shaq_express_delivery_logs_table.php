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
        Schema::create('shaq_express_delivery_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id');
            $table->text('request_payload');
            $table->text('response_payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shaq_express_delivery_logs');
    }
};
