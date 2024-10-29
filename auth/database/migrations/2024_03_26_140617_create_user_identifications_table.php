<?php

use App\Custom\Identification;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_identifications', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique();
            $table->foreignId('user_id');
            $table->string('id_number')->unique()->nullable();
            $table->string('document_url')->nullable();
            $table->string('selfie_url')->nullable();
            $table->enum('type', array_values(Identification::TYPES))->index();
            $table->json('verification_details')->nullable();
            $table->string('status')->default('pending')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_identifications');
    }
};
