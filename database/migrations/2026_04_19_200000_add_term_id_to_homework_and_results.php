<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add term_id to homework
        if (!Schema::hasColumn('homework', 'term_id')) {
            Schema::table('homework', function (Blueprint $table) {
                $table->unsignedBigInteger('term_id')->nullable()->after('academic_year_id');
            });
        }

        // Add term_id to results (keep existing 'term' string column for backward compatibility)
        if (!Schema::hasColumn('results', 'term_id')) {
            Schema::table('results', function (Blueprint $table) {
                $table->unsignedBigInteger('term_id')->nullable()->after('academic_year_id');
            });
        }

        // Add term_id to homework_submissions
        if (!Schema::hasColumn('homework_submissions', 'term_id')) {
            Schema::table('homework_submissions', function (Blueprint $table) {
                $table->unsignedBigInteger('term_id')->nullable()->after('academic_year_id');
            });
        }

        // Backfill: set term_id based on the active term for existing records that have academic_year_id
        $activeTerm = DB::table('terms')->where('is_active', true)->first();
        if ($activeTerm) {
            DB::table('homework')->whereNull('term_id')->where('academic_year_id', $activeTerm->academic_year_id ?? 0)->update(['term_id' => $activeTerm->id]);
            DB::table('homework_submissions')->whereNull('term_id')->update(['term_id' => $activeTerm->id]);

            // Backfill results: match term string to term_id
            $terms = DB::table('terms')->get();
            foreach ($terms as $term) {
                DB::table('results')
                    ->where('term', $term->name)
                    ->whereNull('term_id')
                    ->update(['term_id' => $term->id]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('homework', function (Blueprint $table) {
            $table->dropColumn('term_id');
        });
        Schema::table('results', function (Blueprint $table) {
            $table->dropColumn('term_id');
        });
        Schema::table('homework_submissions', function (Blueprint $table) {
            $table->dropColumn('term_id');
        });
    }
};
