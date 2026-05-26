<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Extend the payment_status enum to include 'carried_forward'.
        // MySQL/MariaDB: must use raw ALTER because Schema::table can't modify enums in-place.
        DB::statement("ALTER TABLE student_fees MODIFY COLUMN payment_status ENUM('unpaid','partial','paid','overpaid','carried_forward') NOT NULL DEFAULT 'unpaid'");

        Schema::table('student_fees', function (Blueprint $table) {
            $table->decimal('previous_balance', 10, 2)
                ->default(0)
                ->after('discount_amount')
                ->comment('Arrears carried in from an earlier term');

            $table->foreignId('carried_forward_to_fee_id')
                ->nullable()
                ->after('previous_balance')
                ->comment('When this row is closed by carry-forward, points at the new term fee that absorbed its balance')
                ->constrained('student_fees')
                ->nullOnDelete();

            $table->index('carried_forward_to_fee_id', 'sf_carried_forward_idx');
        });
    }

    public function down(): void
    {
        Schema::table('student_fees', function (Blueprint $table) {
            $table->dropForeign(['carried_forward_to_fee_id']);
            $table->dropIndex('sf_carried_forward_idx');
            $table->dropColumn(['previous_balance', 'carried_forward_to_fee_id']);
        });

        // Restore original enum (drop any 'carried_forward' rows first or this will fail).
        DB::statement("UPDATE student_fees SET payment_status='unpaid' WHERE payment_status='carried_forward'");
        DB::statement("ALTER TABLE student_fees MODIFY COLUMN payment_status ENUM('unpaid','partial','paid','overpaid') NOT NULL DEFAULT 'unpaid'");
    }
};
