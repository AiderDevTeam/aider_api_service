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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reviewer_id')->index();
            $table->unsignedBigInteger('reviewee_id')->index()->nullable();
            $table->string('external_id')->unique();
            $table->morphs('reviewable');
            $table->unsignedBigInteger('secondary_reviewable_id')->index()->nullable();
            $table->integer('rating')->default(0)->index();
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
