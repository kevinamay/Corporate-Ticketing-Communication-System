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
        Schema::table('tickets', function (Blueprint $table) {
            if (! Schema::hasColumn('tickets', 'target_department_id')) {
                $table->foreignId('target_department_id')->nullable()->after('id')->constrained('departments')->cascadeOnDelete();
            }

            if (! Schema::hasColumn('tickets', 'category')) {
                $table->string('category')->default('General')->after('title');
            }

            if (! Schema::hasColumn('tickets', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('tickets', 'attachment_path')) {
                $table->text('attachment_path')->nullable()->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Safe reverse migration if columns were uniquely added
        });
    }
};
