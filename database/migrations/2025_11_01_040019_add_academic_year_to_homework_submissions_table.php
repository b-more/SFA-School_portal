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
        Schema::table('homework_submissions', function (Blueprint $table) {
            $table->foreignId('academic_year_id')
                ->nullable()
                ->after('homework_id')
                ->constrained('academic_years')
                ->restrictOnDelete();

            $table->index('academic_year_id', 'idx_homework_submissions_academic_year');
        });

        // Auto-populate from homework table
        DB::statement("
            UPDATE homework_submissions hs
            JOIN homework h ON hs.homework_id = h.id
            SET hs.academic_year_id = h.academic_year_id
            WHERE hs.academic_year_id IS NULL
        ");

        Schema::table('homework_submissions', function (Blueprint $table) {
            $table->foreignId('academic_year_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('homework_submissions', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropIndex('idx_homework_submissions_academic_year');
            $table->dropColumn('academic_year_id');
        });
    }
};
