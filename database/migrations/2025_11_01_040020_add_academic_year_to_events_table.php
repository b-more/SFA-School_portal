<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('academic_year_id')
                ->nullable()
                ->after('organizer_id')
                ->constrained('academic_years')
                ->restrictOnDelete();

            $table->index('academic_year_id', 'idx_events_academic_year');
            $table->index(['academic_year_id', 'start_date'], 'idx_events_year_start_date');
            $table->index(['academic_year_id', 'status'], 'idx_events_year_status');
        });

        // Assign to year based on event start_date
        DB::statement("
            UPDATE events e
            JOIN academic_years ay ON e.start_date BETWEEN ay.start_date AND ay.end_date
            SET e.academic_year_id = ay.id
            WHERE e.academic_year_id IS NULL
        ");

        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('academic_year_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropIndex('idx_events_academic_year');
            $table->dropIndex('idx_events_year_start_date');
            $table->dropIndex('idx_events_year_status');
            $table->dropColumn('academic_year_id');
        });
    }
};
