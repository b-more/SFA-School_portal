<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('academic_year_id')
                ->nullable()
                ->after('enrollment_term_id')
                ->constrained('academic_years')
                ->restrictOnDelete();

            $table->index('academic_year_id', 'idx_students_academic_year');
            $table->index(['academic_year_id', 'grade_id'], 'idx_students_year_grade');
            $table->index(['academic_year_id', 'enrollment_status'], 'idx_students_year_status');
            $table->index(['student_id_number', 'academic_year_id'], 'idx_students_id_year');
        });

        // Populate existing records with 2025 academic year
        $currentYearId = DB::table('academic_years')
            ->where('is_active', true)
            ->value('id');

        if ($currentYearId) {
            DB::table('students')
                ->whereNull('academic_year_id')
                ->update(['academic_year_id' => $currentYearId]);
        }

        // Make NOT NULL after populating
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('academic_year_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropIndex('idx_students_academic_year');
            $table->dropIndex('idx_students_year_grade');
            $table->dropIndex('idx_students_year_status');
            $table->dropIndex('idx_students_id_year');
            $table->dropColumn('academic_year_id');
        });
    }
};
