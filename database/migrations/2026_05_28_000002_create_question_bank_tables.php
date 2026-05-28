<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_bank_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->index();      // teacher id
            $table->unsignedBigInteger('subject_id')->nullable()->index();
            $table->unsignedBigInteger('grade_id')->nullable()->index();
            $table->string('topic')->nullable();
            $table->enum('curriculum', ['ordinary', 'cbc'])->default('ordinary')->index();
            $table->enum('component', ['theory', 'sba'])->nullable();   // CBC: theory or SBA
            $table->enum('type', ['mcq', 'true_false', 'structured', 'scenario']);
            $table->longText('question_text');
            $table->unsignedInteger('max_marks')->default(1);
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->nullable();
            $table->longText('model_answer')->nullable();
            $table->boolean('is_shared')->default(false);
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->unsignedBigInteger('term_id')->nullable();
            $table->timestamps();
        });

        Schema::create('question_bank_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_bank_item_id')->constrained('question_bank_items')->cascadeOnDelete();
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->unsignedInteger('position')->default(0);
        });

        Schema::create('question_bank_rubric_criteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_bank_item_id')->constrained('question_bank_items')->cascadeOnDelete();
            $table->text('criterion');
            $table->unsignedInteger('max_marks')->default(1);
            $table->unsignedInteger('position')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_bank_rubric_criteria');
        Schema::dropIfExists('question_bank_options');
        Schema::dropIfExists('question_bank_items');
    }
};
