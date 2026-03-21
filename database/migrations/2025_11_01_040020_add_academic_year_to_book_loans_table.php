<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('book_loans', function (Blueprint $table) {
            $table->foreignId('academic_year_id')
                ->nullable()
                ->after('student_id')
                ->constrained('academic_years')
                ->restrictOnDelete();

            $table->index('academic_year_id', 'idx_book_loans_academic_year');
            $table->index(['academic_year_id', 'status'], 'idx_book_loans_year_status');
            $table->index(['academic_year_id', 'lent_date'], 'idx_book_loans_year_lent_date');
        });

        // Set to current active year
        $currentYearId = DB::table('academic_years')->where('is_active', true)->value('id');
        if ($currentYearId) {
            DB::table('book_loans')->whereNull('academic_year_id')->update(['academic_year_id' => $currentYearId]);
        }

        Schema::table('book_loans', function (Blueprint $table) {
            $table->foreignId('academic_year_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('book_loans', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropIndex('idx_book_loans_academic_year');
            $table->dropIndex('idx_book_loans_year_status');
            $table->dropIndex('idx_book_loans_year_lent_date');
            $table->dropColumn('academic_year_id');
        });
    }
};
