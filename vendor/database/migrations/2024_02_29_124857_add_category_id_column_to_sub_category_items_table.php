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
        Schema::table('sub_category_items', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()
                ->constrained()->onDelete('cascade')
                ->onUpdate('cascade')->after('sub_category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_category_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
        });
    }
};
