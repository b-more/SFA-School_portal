<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('student_bus_assignments')) {
            Schema::create('student_bus_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
                $table->unsignedBigInteger('bus_fare_structure_id')->nullable();
                $table->decimal('monthly_amount', 10, 2);
                $table->date('started_at');
                $table->date('ended_at')->nullable();
                $table->boolean('is_active')->default(true);
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->index(['student_id', 'is_active']);
                $table->index('bus_fare_structure_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('student_bus_assignments');
    }
};
