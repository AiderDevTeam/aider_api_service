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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('can_receive_email_updates')->default(true)->after('id_verification_status');
            $table->boolean('can_receive_push_notifications')->default(true)->after('can_receive_email_updates');
            $table->boolean('can_receive_sms')->default(true)->after('can_receive_email_updates');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('can_receive_email_updates');
            $table->dropColumn('can_receive_push_notifications');
            $table->dropColumn('can_receive_sms');
        });
    }
};
