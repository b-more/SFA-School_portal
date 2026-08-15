<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinic_complaints', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('medical_stock_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('category', ['tablet', 'syrup', 'gel_ointment', 'lozenge', 'first_aid', 'other'])->default('other');
            $table->string('unit')->default('pieces');   // tablets, ml, sachets, pieces
            $table->integer('reorder_level')->default(10);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('clinic_visits', function (Blueprint $table) {
            $table->id();
            $table->date('visit_date');
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->string('student_name');
            $table->string('grade');                  // free-text as recorded (e.g. "10", "Form 1")
            $table->unsignedTinyInteger('grade_level')->nullable();   // normalized 1..12
            $table->text('complaint_notes')->nullable();
            $table->boolean('sick_note_issued')->default(false);
            $table->enum('outcome', ['returned_to_class', 'sent_home', 'referred'])->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('needs_review')->default(false);
            $table->timestamps();

            $table->index('visit_date');
            $table->index('student_id');
        });

        Schema::create('clinic_visit_clinic_complaint', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_visit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('clinic_complaint_id')->constrained('clinic_complaints')->cascadeOnDelete();
            $table->unique(['clinic_visit_id', 'clinic_complaint_id'], 'cvcc_unique');
        });

        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_stock_item_id')->constrained()->cascadeOnDelete();
            $table->enum('transaction_type', ['purchase', 'usage', 'adjustment', 'expired_damaged', 'opening']);
            $table->integer('quantity');            // always positive; sign determined by type
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->string('supplier')->nullable();
            $table->foreignId('clinic_visit_id')->nullable()->constrained()->nullOnDelete();
            $table->date('transaction_date');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['medical_stock_item_id', 'transaction_type']);
            $table->index('transaction_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
        Schema::dropIfExists('clinic_visit_clinic_complaint');
        Schema::dropIfExists('clinic_visits');
        Schema::dropIfExists('medical_stock_items');
        Schema::dropIfExists('clinic_complaints');
    }
};
