<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qr_payments', function (Blueprint $table) {
            if (! Schema::hasColumn('qr_payments', 'bus_fare_structure_id')) {
                $table->unsignedBigInteger('bus_fare_structure_id')->nullable()->after('period_label');
                $table->index('bus_fare_structure_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('qr_payments', function (Blueprint $table) {
            if (Schema::hasColumn('qr_payments', 'bus_fare_structure_id')) {
                try { $table->dropIndex(['bus_fare_structure_id']); } catch (\Throwable $e) {}
                $table->dropColumn('bus_fare_structure_id');
            }
        });
    }
};
