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
        Schema::create('user_search_records', function (Blueprint $table) {
            $table->id();
            $table->string('user_external_id');
            $table->foreign('user_external_id')->references('external_id')->on('users')->onDelete('cascade');
            $table->string('search_term')->index();
            $table->integer('profiles_found');
            $table->integer('products_found');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_search_records');
    }
};
