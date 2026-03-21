<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the enum to add 'end-of-term' option
        DB::statement("ALTER TABLE results MODIFY COLUMN exam_type ENUM('mid-term', 'final', 'quiz', 'assignment', 'end-of-term') DEFAULT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE results MODIFY COLUMN exam_type ENUM('mid-term', 'final', 'quiz', 'assignment') DEFAULT NULL");
    }
};
