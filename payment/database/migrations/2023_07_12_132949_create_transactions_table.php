<?php

use App\Enum\Status;
use App\Models\Transaction;
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
            $table->string('external_id')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('payment_id');
            $table->string('stan')->unique();
            $table->decimal('amount');
            $table->string('account_number')->index();
            $table->string('status')->default(Status::STARTED->value);
            $table->string('sort_code')->nullable();
            $table->string('description')->nullable();

            $table->enum('type', array_values(Transaction::TYPES));

            $table->string('callback_url')->nullable();
            $table->json('response_payload')->nullable();
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
