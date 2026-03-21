<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('homework', function (Blueprint $table) {
            $table->foreignId('academic_year_id')
                ->nullable()
                ->after('grade_id')
                ->constrained('academic_years')
                ->restrictOnDelete();

            $table->index('academic_year_id', 'idx_homework_academic_year');
            $table->index(['academic_year_id', 'grade_id', 'subject_id'], 'idx_homework_year_grade_subject');
            $table->index(['academic_year_id', 'due_date'], 'idx_homework_year_due_date');
        });

        $currentYearId = DB::table('academic_years')->where('is_active', true)->value('id');
        if ($currentYearId) {
            DB::table('homework')->whereNull('academic_year_id')->update(['academic_year_id' => $currentYearId]);
        }

        Schema::table('homework', function (Blueprint $table) {
            $table->foreignId('academic_year_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('homework', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropIndex('idx_homework_academic_year');
            $table->dropIndex('idx_homework_year_grade_subject');
            $table->dropIndex('idx_homework_year_due_date');
            $table->dropColumn('academic_year_id');
        });
    }
};
