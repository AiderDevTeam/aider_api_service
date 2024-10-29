<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE products MODIFY COLUMN `value` DOUBLE(20, 2)');
        DB::statement('ALTER TABLE product_prices MODIFY COLUMN `price` DOUBLE(20, 2)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE products MODIFY COLUMN `value` DOUBLE(8,2)');
        DB::statement('ALTER TABLE product_prices MODIFY COLUMN `price` DOUBLE(8,2)');
    }
};
