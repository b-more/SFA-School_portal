<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bus_boarding_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_fare_structure_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->enum('status', ['boarded', 'absent', 'no_show']);
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['bus_fare_structure_id', 'student_id', 'date'], 'bbl_route_student_date_unique');
            $table->index('date');
            $table->index(['student_id', 'date']);
            $table->index(['bus_fare_structure_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bus_boarding_logs');
    }
};
