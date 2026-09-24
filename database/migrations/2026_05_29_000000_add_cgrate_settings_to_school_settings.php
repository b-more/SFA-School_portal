<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('school_settings', 'cgrate_soap_url')) {
                $table->string('cgrate_soap_url')->nullable()->after('mobile_money_details');
            }
            if (! Schema::hasColumn('school_settings', 'cgrate_username')) {
                $table->string('cgrate_username')->nullable()->after('cgrate_soap_url');
            }
            if (! Schema::hasColumn('school_settings', 'cgrate_password')) {
                // Stores an encrypted blob (Laravel 'encrypted' cast), so use text.
                $table->text('cgrate_password')->nullable()->after('cgrate_username');
            }
            if (! Schema::hasColumn('school_settings', 'cgrate_timeout')) {
                $table->unsignedSmallInteger('cgrate_timeout')->nullable()->default(30)->after('cgrate_password');
            }
        });
    }

    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            foreach (['cgrate_soap_url', 'cgrate_username', 'cgrate_password', 'cgrate_timeout'] as $column) {
                if (Schema::hasColumn('school_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
