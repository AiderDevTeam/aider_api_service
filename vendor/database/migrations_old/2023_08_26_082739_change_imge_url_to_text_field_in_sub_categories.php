<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sub_categories', function (Blueprint $table) {
            //
        });

        $this->changeColumnType('sub_categories', 'image_url', 'TEXT(500)');
    }

    public function changeColumnType($table, $column, $newColumnType) {                
        DB::statement("ALTER TABLE $table CHANGE $column $column $newColumnType");
    } 
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_categories', function (Blueprint $table) {
            //
        });
    }
};
