<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('school_settings', 'sibling_discount_enabled')) {
                $table->boolean('sibling_discount_enabled')->default(false)->after('report_card_lock_threshold');
                $table->unsignedTinyInteger('sibling_discount_min_pupils')->default(4);
                $table->decimal('sibling_discount_percentage', 5, 2)->default(10);
            }
            if (! Schema::hasColumn('school_settings', 'early_payment_discount_enabled')) {
                $table->boolean('early_payment_discount_enabled')->default(false);
                $table->decimal('early_payment_discount_percentage', 5, 2)->default(5);
            }
        });
    }

    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            foreach ([
                'sibling_discount_enabled', 'sibling_discount_min_pupils', 'sibling_discount_percentage',
                'early_payment_discount_enabled', 'early_payment_discount_percentage',
            ] as $col) {
                if (Schema::hasColumn('school_settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
