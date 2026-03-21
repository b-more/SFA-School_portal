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
        Schema::table('fee_structures', function (Blueprint $table) {
            // Add school_section_id (nullable for backward compatibility)
            $table->unsignedBigInteger('school_section_id')->nullable()->after('grade_id');

            // Make grade_id nullable (new records won't have it)
            $table->unsignedBigInteger('grade_id')->nullable()->change();

            // Drop old unique index
            $table->dropUnique(['grade_id', 'term_id', 'academic_year_id']);

            // Add new unique index for section-based fees
            // MySQL ignores nulls in unique indexes, so old records (with null school_section_id) won't conflict
            $table->unique(['school_section_id', 'term_id', 'academic_year_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fee_structures', function (Blueprint $table) {
            // Drop new unique index
            $table->dropUnique(['school_section_id', 'term_id', 'academic_year_id']);

            // Remove school_section_id column
            $table->dropColumn('school_section_id');

            // Make grade_id non-nullable again
            $table->unsignedBigInteger('grade_id')->nullable(false)->change();

            // Restore old unique index
            $table->unique(['grade_id', 'term_id', 'academic_year_id']);
        });
    }
};
