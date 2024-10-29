<?php

use App\Enum\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->morphs('paymentable');
            $table->string('external_id')->unique();

            $table->decimal('collection_amount')->nullable();
            $table->string('collection_status')->nullable();
            $table->string('collection_account_number')->nullable();
            $table->string('collection_account_sort_code')->nullable();

            $table->decimal('disbursement_amount')->nullable();
            $table->string('disbursement_status')->nullable();
            $table->string('disbursement_account_number')->nullable();
            $table->string('disbursement_account_sort_code')->nullable();

            $table->string('reversal_status')->nullable();

            $table->json('response_payload')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
