<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qr_payments', function (Blueprint $table) {
            // Where the payment originated from. Nullable so pre-existing rows
            // stay untouched. New rows written by ParentApiController use
            // 'parent_app'; PublicPaymentController uses 'public_pay'; the
            // WhatsApp bot uses 'whatsapp_bot'. Reconciliation looks at this
            // to decide whether to notify the bot after crediting.
            $table->string('channel', 32)->nullable()->after('payment_kind');
            $table->index('channel');
        });
    }

    public function down(): void
    {
        Schema::table('qr_payments', function (Blueprint $table) {
            $table->dropIndex(['channel']);
            $table->dropColumn('channel');
        });
    }
};
