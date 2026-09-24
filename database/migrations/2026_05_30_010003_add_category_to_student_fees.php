<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_fees', function (Blueprint $table) {
            if (! Schema::hasColumn('student_fees', 'fee_category_id')) {
                $table->foreignId('fee_category_id')->nullable()->after('fee_structure_id')->constrained('fee_categories');
            }
            if (! Schema::hasColumn('student_fees', 'period_label')) {
                $table->string('period_label', 64)->nullable()->after('fee_category_id');
            }
            if (! Schema::hasColumn('student_fees', 'catalogue_item_id')) {
                $table->foreignId('catalogue_item_id')->nullable()->after('period_label')->constrained('fee_catalogue_items');
            }
        });

        // Non-tuition categories don't link to a fee_structure row, so the FK column must allow NULL.
        try {
            Schema::table('student_fees', function (Blueprint $table) {
                $table->unsignedBigInteger('fee_structure_id')->nullable()->change();
            });
        } catch (\Throwable $e) {
            // Column may already be nullable — fine.
        }

        // Backfill: every existing student_fee row is tuition. Period label = "{term} {year}".
        $tuitionId = DB::table('fee_categories')->where('code', 'tuition')->value('id');
        if (! $tuitionId) return; // safety: seeding skipped (shouldn't happen)

        DB::table('student_fees')->whereNull('fee_category_id')->update(['fee_category_id' => $tuitionId]);

        // Backfill period_label from terms + academic_years where available.
        $rows = DB::table('student_fees as sf')
            ->leftJoin('terms as t', 't.id', '=', 'sf.term_id')
            ->leftJoin('academic_years as y', 'y.id', '=', 'sf.academic_year_id')
            ->whereNull('sf.period_label')
            ->select('sf.id', 't.name as term_name', 'y.name as year_name')
            ->get();
        foreach ($rows as $r) {
            $label = trim(($r->term_name ?? 'Term') . ' ' . ($r->year_name ?? ''));
            DB::table('student_fees')->where('id', $r->id)->update(['period_label' => $label]);
        }
    }

    public function down(): void
    {
        Schema::table('student_fees', function (Blueprint $table) {
            foreach (['catalogue_item_id', 'period_label', 'fee_category_id'] as $col) {
                if (Schema::hasColumn('student_fees', $col)) {
                    try { $table->dropForeign(["student_fees_{$col}_foreign"]); } catch (\Throwable $e) {}
                    $table->dropColumn($col);
                }
            }
        });
    }
};
