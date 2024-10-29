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
        Schema::create('user_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->unsignedInteger('sold_items_count')->default(0);
            $table->unsignedInteger('listed_items_count')->default(0);
            $table->decimal('rating', 5, 1)->default(0)->index();
            $table->json('individual_rating_counts')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_statistics');
    }
};
