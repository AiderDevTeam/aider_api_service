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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('sub_category_item_id');
            $table->integer('quantity');
            $table->string('status')->default('active')->index();
            $table->string('name')->index();
            $table->fulltext('name');
            $table->mediumText('description');
            $table->double('value', 8, 2);
            $table->decimal('rating', 5, 1)->index()->default(0);
            $table->string('share_link')->unique()->nullable();
            $table->datetime('approval_date')->nullable();
            $table->datetime('rejection_date')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
