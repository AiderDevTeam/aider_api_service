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
        DB::statement('ALTER TABLE products ADD FULLTEXT(name)');
        DB::statement('ALTER TABLE products ADD FULLTEXT(description)');
        DB::statement('ALTER TABLE products ADD FULLTEXT(color)');
        DB::statement('ALTER TABLE product_tags ADD FULLTEXT(name)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE products DROP INDEX name');
        DB::statement('ALTER TABLE products DROP INDEX description');
        DB::statement('ALTER TABLE products DROP INDEX color');
        DB::statement('ALTER TABLE product_tags DROP INDEX name');
    }
};
