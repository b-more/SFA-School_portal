<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // CBC scenario "Theory" assessments — pupil-answered, teacher rubric-marked.
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->index();      // teacher
            $table->unsignedBigInteger('class_section_id')->index();
            $table->unsignedBigInteger('subject_id')->nullable()->index();
            $table->unsignedBigInteger('grade_id')->nullable()->index();
            $table->string('title');
            $table->longText('description')->nullable();
            $table->enum('component', ['theory', 'sba'])->default('theory');
            $table->unsignedInteger('time_limit_minutes')->nullable();
            $table->dateTime('due_at')->nullable();
            $table->enum('status', ['draft', 'published', 'closed'])->default('published');
            $table->unsignedInteger('total_marks')->default(0);
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->unsignedBigInteger('term_id')->nullable();
            $table->timestamps();
        });

        Schema::create('assessment_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete();
            $table->longText('question_text');
            $table->unsignedInteger('points')->default(0);
            $table->unsignedInteger('position')->default(0);
            $table->unsignedBigInteger('source_bank_item_id')->nullable();
            $table->timestamps();
        });

        Schema::create('assessment_question_criteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_question_id')->constrained('assessment_questions')->cascadeOnDelete();
            $table->text('criterion');
            $table->unsignedInteger('max_marks')->default(1);
            $table->unsignedInteger('position')->default(0);
        });

        Schema::create('assessment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete();
            $table->unsignedBigInteger('student_id')->index();
            $table->dateTime('submitted_at')->nullable();
            $table->enum('status', ['submitted', 'marked'])->default('submitted');
            $table->decimal('total_score', 7, 2)->nullable();
            $table->unsignedInteger('total_marks')->default(0);
            $table->decimal('percentage', 5, 2)->nullable();
            $table->unsignedBigInteger('marked_by')->nullable();
            $table->dateTime('marked_at')->nullable();
            $table->timestamps();
            $table->unique(['assessment_id', 'student_id']);
        });

        Schema::create('assessment_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_submission_id')->constrained('assessment_submissions')->cascadeOnDelete();
            $table->unsignedBigInteger('assessment_question_id')->index();
            $table->longText('response_text')->nullable();
        });

        Schema::create('assessment_criterion_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_submission_id')->constrained('assessment_submissions')->cascadeOnDelete();
            $table->unsignedBigInteger('assessment_question_criterion_id')->index('acm_criterion_idx');
            $table->decimal('marks_awarded', 6, 2)->default(0);
            $table->unique(['assessment_submission_id', 'assessment_question_criterion_id'], 'acm_submission_criterion_unique');
        });

        // SBA component — teacher-recorded continuous-assessment mark per pupil/subject/term.
        Schema::create('sba_marks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id')->index();
            $table->unsignedBigInteger('subject_id')->index();
            $table->unsignedBigInteger('class_section_id')->nullable()->index();
            $table->unsignedBigInteger('recorded_by')->nullable();
            $table->decimal('score', 6, 2)->default(0);
            $table->decimal('max_score', 6, 2)->default(100);
            $table->unsignedBigInteger('term_id')->nullable();
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'subject_id', 'term_id'], 'sba_student_subject_term_unique');
        });

        // Configurable ECZ weighting (single row).
        Schema::create('ecz_assessment_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('theory_weight')->default(70);
            $table->unsignedInteger('sba_weight')->default(30);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecz_assessment_settings');
        Schema::dropIfExists('sba_marks');
        Schema::dropIfExists('assessment_criterion_marks');
        Schema::dropIfExists('assessment_answers');
        Schema::dropIfExists('assessment_submissions');
        Schema::dropIfExists('assessment_question_criteria');
        Schema::dropIfExists('assessment_questions');
        Schema::dropIfExists('assessments');
    }
};
