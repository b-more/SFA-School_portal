<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->foreignId('academic_year_id')
                ->nullable()
                ->after('employee_id')
                ->constrained('academic_years')
                ->restrictOnDelete();

            $table->index('academic_year_id', 'idx_payrolls_academic_year');
            $table->index(['academic_year_id', 'month', 'year'], 'idx_payrolls_year_month');
            $table->index(['academic_year_id', 'employee_id'], 'idx_payrolls_year_employee');
        });

        // Map from year column
        DB::statement("
            UPDATE payrolls p
            JOIN academic_years ay ON ay.name = p.year
            SET p.academic_year_id = ay.id
            WHERE p.academic_year_id IS NULL
        ");

        Schema::table('payrolls', function (Blueprint $table) {
            $table->foreignId('academic_year_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropIndex('idx_payrolls_academic_year');
            $table->dropIndex('idx_payrolls_year_month');
            $table->dropIndex('idx_payrolls_year_employee');
            $table->dropColumn('academic_year_id');
        });
    }
};
