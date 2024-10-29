<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->index();
            $table->string('account_number')->index();
            $table->unsignedFloat('amount', 20)->index();
            $table->string('status')->nullable()->index();
            $table->string('message')->nullable()->index();
            $table->string('callback_url')->index();
            $table->string('description')->nullable();
            $table->string('stan')->nullable();
            $table->string('code')->nullable();
            $table->string('switch_code')->nullable();
            $table->string('success')->nullable()->index();
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_logs');
    }
};
