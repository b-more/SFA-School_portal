<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->foreignId('academic_year_id')
                ->nullable()
                ->after('year')
                ->constrained('academic_years')
                ->restrictOnDelete();

            $table->index('academic_year_id', 'idx_results_academic_year');
            $table->index(['academic_year_id', 'student_id'], 'idx_results_year_student');
            $table->index(['academic_year_id', 'term'], 'idx_results_year_term');
            $table->index(['academic_year_id', 'subject_id'], 'idx_results_year_subject');
        });

        // Populate from 'year' column
        DB::statement("
            UPDATE results r
            JOIN academic_years ay ON ay.name = r.year
            SET r.academic_year_id = ay.id
            WHERE r.academic_year_id IS NULL
        ");

        Schema::table('results', function (Blueprint $table) {
            $table->foreignId('academic_year_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropIndex('idx_results_academic_year');
            $table->dropIndex('idx_results_year_student');
            $table->dropIndex('idx_results_year_term');
            $table->dropIndex('idx_results_year_subject');
            $table->dropColumn('academic_year_id');
        });
    }
};
