<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->foreignId('academic_year_id')
                ->nullable()
                ->after('student_fee_id')
                ->constrained('academic_years')
                ->restrictOnDelete();

            $table->index('academic_year_id', 'idx_payment_transactions_academic_year');
            $table->index(['academic_year_id', 'transaction_date'], 'idx_payment_trans_year_date');
            $table->index(['academic_year_id', 'payment_method'], 'idx_payment_trans_year_method');
            $table->index(['academic_year_id', 'status'], 'idx_payment_trans_year_status');
        });

        // Auto-populate from student_fees table
        DB::statement("
            UPDATE payment_transactions pt
            JOIN student_fees sf ON pt.student_fee_id = sf.id
            SET pt.academic_year_id = sf.academic_year_id
            WHERE pt.academic_year_id IS NULL
        ");

        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->foreignId('academic_year_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropIndex('idx_payment_transactions_academic_year');
            $table->dropIndex('idx_payment_trans_year_date');
            $table->dropIndex('idx_payment_trans_year_method');
            $table->dropIndex('idx_payment_trans_year_status');
            $table->dropColumn('academic_year_id');
        });
    }
};
