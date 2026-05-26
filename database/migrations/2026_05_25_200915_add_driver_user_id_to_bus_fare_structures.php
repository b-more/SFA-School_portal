<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bus_fare_structures', function (Blueprint $table) {
            $table->foreignId('driver_user_id')
                ->nullable()
                ->after('term_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->index('driver_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('bus_fare_structures', function (Blueprint $table) {
            $table->dropForeign(['driver_user_id']);
            $table->dropIndex(['driver_user_id']);
            $table->dropColumn('driver_user_id');
        });
    }
};
