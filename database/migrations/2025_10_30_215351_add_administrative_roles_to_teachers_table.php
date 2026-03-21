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
        Schema::table('teachers', function (Blueprint $table) {
            // Administrative role type
            $table->enum('administrative_role', [
                'none',
                'director',
                'head_teacher',
                'deputy_head_teacher',
                'dean_of_students'
            ])->default('none')->after('is_class_teacher');

            // Section scope for head teacher and deputy head teacher
            $table->enum('section_scope', [
                'none',
                'primary',      // Baby Class to Grade 7
                'secondary',    // Grade 8 to Grade 12
                'hybrid'        // Both Primary and Secondary
            ])->default('none')->after('administrative_role');

            // Allow these administrative roles to not have grade/class assignments
            $table->boolean('requires_class_assignment')->default(true)->after('section_scope');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn(['administrative_role', 'section_scope', 'requires_class_assignment']);
        });
    }
};
