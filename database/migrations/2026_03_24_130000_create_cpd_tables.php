<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cpd_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->enum('type', ['workshop', 'course', 'conference', 'seminar', 'peer_observation', 'self_study', 'mentoring', 'online_training', 'research', 'other'])->default('workshop');
            $table->string('provider')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('hours', 6, 1)->default(0);
            $table->text('description')->nullable();
            $table->text('reflection')->nullable();
            $table->text('key_learnings')->nullable();
            $table->string('certificate_file')->nullable();
            $table->enum('status', ['planned', 'in_progress', 'completed'])->default('completed');
            $table->string('academic_year')->nullable();
            $table->string('term')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        Schema::create('cpd_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('term')->nullable();
            $table->string('academic_year')->nullable();
            $table->enum('status', ['not_started', 'in_progress', 'achieved'])->default('not_started');
            $table->date('target_date')->nullable();
            $table->date('achieved_date')->nullable();
            $table->text('evidence')->nullable();
            $table->timestamps();
        });

        Schema::create('cpd_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('subject')->nullable();
            $table->string('grade')->nullable();
            $table->enum('type', ['lesson_plan', 'worksheet', 'past_paper', 'presentation', 'video_link', 'article', 'template', 'other'])->default('other');
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('external_url')->nullable();
            $table->unsignedInteger('download_count')->default(0);
            $table->timestamps();

            $table->index('subject');
        });

        Schema::create('cpd_observations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('observer_user_id')->constrained('users')->onDelete('cascade');
            $table->date('observation_date');
            $table->string('subject')->nullable();
            $table->string('class_observed')->nullable();
            $table->string('topic')->nullable();
            $table->text('strengths')->nullable();
            $table->text('areas_for_improvement')->nullable();
            $table->text('recommendations')->nullable();
            $table->text('teacher_reflection')->nullable();
            $table->unsignedTinyInteger('rating')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cpd_observations');
        Schema::dropIfExists('cpd_resources');
        Schema::dropIfExists('cpd_goals');
        Schema::dropIfExists('cpd_activities');
    }
};
