<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bus_boarding_logs', function (Blueprint $table) {
            $table->enum('trip', ['to_school', 'from_school'])
                ->default('to_school')
                ->after('date');
        });

        // Replace (route, student, date) unique with (route, student, date, trip).
        Schema::table('bus_boarding_logs', function (Blueprint $table) {
            $table->dropUnique('bbl_route_student_date_unique');
        });

        Schema::table('bus_boarding_logs', function (Blueprint $table) {
            $table->unique(
                ['bus_fare_structure_id', 'student_id', 'date', 'trip'],
                'bbl_route_student_date_trip_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('bus_boarding_logs', function (Blueprint $table) {
            $table->dropUnique('bbl_route_student_date_trip_unique');
        });

        Schema::table('bus_boarding_logs', function (Blueprint $table) {
            $table->unique(
                ['bus_fare_structure_id', 'student_id', 'date'],
                'bbl_route_student_date_unique'
            );

            $table->dropColumn('trip');
        });
    }
};
