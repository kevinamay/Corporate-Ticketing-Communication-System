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
        Schema::table('users', function (Blueprint $table) {
            $table->string('national_id_ktp')->nullable()->unique()->after('password');
            $table->enum('gender', ['male', 'female'])->default('male')->after('national_id_ktp');
            $table->string('whatsapp_number')->nullable()->after('gender');
            $table->text('complete_address')->nullable()->after('whatsapp_number');
            $table->string('postal_code')->nullable()->after('complete_address');
            $table->string('otp_code', 10)->nullable()->after('avatar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'national_id_ktp',
                'gender',
                'whatsapp_number',
                'complete_address',
                'postal_code',
                'otp_code',
            ]);
        });
    }
};
