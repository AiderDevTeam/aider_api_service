<?php

use App\Custom\Status;
use App\Models\User;
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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('birthday')->index()->nullable();
            $table->enum('gender', User::GENDERS);
            $table->string('phone')->unique();
            $table->string('calling_code')->nullable();
            $table->string('profile_photo_url')->nullable();
            $table->string('status')->index()->default(Status::ACTIVE);
            $table->string('push_notification_token')->nullable();
            $table->boolean('push_notification_token_update')->default(false);
            $table->string('referral_code')->unique()->nullable();
            $table->string('referral_url')->unique()->nullable();
            $table->string('device_os')->nullable()->index();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('terms_and_conditions')->index();
            $table->string('password');
            $table->string('id_number')->unique()->nullable();
            $table->string('id_type')->nullable()->index();
            $table->boolean('id_verified')->default(false)->index();
            $table->string('id_verified_at')->nullable()->index();
            $table->string('photo_on_id_url')->nullable();
            $table->string('id_photo_url')->nullable();
            $table->string('selfie_url')->nullable();
            $table->string('signature_url')->nullable();
            $table->string('id_verification_status')->default('not started');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
