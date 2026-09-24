<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('school_settings', 'report_card_lock_threshold')) {
                // Outstanding balance allowed before report cards / results are locked.
                // 0 = strict (any balance locks). Default K100 forgives tiny leftovers.
                $table->decimal('report_card_lock_threshold', 10, 2)->default(100)->after('cgrate_timeout');
            }
        });
    }

    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            if (Schema::hasColumn('school_settings', 'report_card_lock_threshold')) {
                $table->dropColumn('report_card_lock_threshold');
            }
        });
    }
};
