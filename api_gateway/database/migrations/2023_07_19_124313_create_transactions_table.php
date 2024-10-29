<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->index();
            $table->mediumInteger('amount');
            $table->string('stan')->unique();
            $table->string('r_switch')->index();
            $table->string('account_number')->index();
            $table->string('recipient_code')->index()->nullable();
            $table->string('transfer_code')->index()->nullable();
            $table->string('status')->index();
            $table->string('type')->index();
            $table->string('description')->nullable();
            $table->string('response_code')->index()->nullable();
            $table->string('response_message')->nullable();
            $table->string('callback_url')->nullable();
            $table->morphs('processor');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
