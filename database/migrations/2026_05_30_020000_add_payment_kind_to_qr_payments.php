<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qr_payments', function (Blueprint $table) {
            if (! Schema::hasColumn('qr_payments', 'payment_kind')) {
                // 'general' = normal oldest-first apply; 'bus_month' = pay-as-you-go bus.
                $table->string('payment_kind', 24)->default('general')->after('amount');
            }
            if (! Schema::hasColumn('qr_payments', 'period_label')) {
                // For bus_month: "May 2026". Lets us idempotency-check per (student × month).
                $table->string('period_label', 64)->nullable()->after('payment_kind');
            }
        });
    }

    public function down(): void
    {
        Schema::table('qr_payments', function (Blueprint $table) {
            foreach (['period_label', 'payment_kind'] as $c) {
                if (Schema::hasColumn('qr_payments', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};
