<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->enum('priority', ['normal', 'important', 'urgent'])->default('normal');

            // Target audience
            $table->enum('target_type', ['school', 'section', 'grade', 'class', 'student'])->default('school');
            $table->unsignedBigInteger('target_section_id')->nullable(); // SchoolSection ID
            $table->unsignedBigInteger('target_grade_id')->nullable();   // Grade ID
            $table->unsignedBigInteger('target_class_id')->nullable();   // ClassSection ID
            $table->unsignedBigInteger('target_student_id')->nullable(); // Student ID

            $table->string('attachment')->nullable();
            $table->unsignedBigInteger('posted_by')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['target_type', 'is_active', 'published_at']);
            $table->index('target_grade_id');
            $table->index('target_class_id');
            $table->index('target_student_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notices');
    }
};
