<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('id_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('id_number')->unique();
            $table->string('type')->index();
            $table->boolean('verified')->default(false)->index();
            $table->string('photo_on_id_url')->nullable();
            $table->string('card_id')->index()->nullable();
            $table->string('card_valid_from')->nullable();
            $table->string('card_valid_to')->nullable();
            $table->string('surname')->nullable();
            $table->string('forenames')->nullable();
            $table->string('nationality')->index()->nullable();
            $table->string('birth_date')->nullable();
            $table->string('gender')->index()->nullable();
            $table->string('email')->index()->nullable();
            $table->string('phone_number')->index()->nullable();
            $table->string('birth_country')->nullable();
            $table->string('birth_district')->nullable();
            $table->string('birth_region')->nullable();
            $table->string('birth_town')->nullable();
            $table->string('home_town')->nullable();
            $table->string('home_town_country')->nullable();
            $table->string('home_town_district')->nullable();
            $table->string('home_town_region')->nullable();
            $table->string('residence')->nullable();
            $table->string('residence_street')->nullable();
            $table->string('residence_district')->nullable();
            $table->string('residence_postal_code')->nullable();
            $table->string('residence_region')->nullable();
            $table->string('residence_digital_address')->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->string('occupation')->nullable();
            $table->string('signature_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('id_verifications');
    }
};
