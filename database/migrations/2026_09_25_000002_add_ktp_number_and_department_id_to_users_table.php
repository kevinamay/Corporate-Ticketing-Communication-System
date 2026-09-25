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
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'ktp_number')) {
                $table->string('ktp_number')->nullable()->unique()->after('password');
            }

            if (! Schema::hasColumn('users', 'department_id')) {
                $table->foreignId('department_id')->nullable()->after('password')->constrained('departments')->nullOnDelete();
            }
        });

        // Sync existing national_id_ktp to ktp_number if present
        if (Schema::hasColumn('users', 'national_id_ktp') && Schema::hasColumn('users', 'ktp_number')) {
            DB::table('users')
                ->whereNull('ktp_number')
                ->whereNotNull('national_id_ktp')
                ->update(['ktp_number' => DB::raw('national_id_ktp')]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'ktp_number')) {
                $table->dropColumn('ktp_number');
            }
        });
    }
};
