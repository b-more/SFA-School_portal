<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cpd_activities', function (Blueprint $table) {
            $table->boolean('is_mandatory')->default(false)->after('status');
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('is_mandatory');
            $table->unsignedBigInteger('approved_by')->nullable()->after('approval_status');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->string('approval_remarks')->nullable()->after('approved_at');
            $table->decimal('points', 6, 1)->default(0)->after('approval_remarks');
            $table->unsignedBigInteger('goal_id')->nullable()->after('points');
            $table->unsignedBigInteger('observation_id')->nullable()->after('goal_id');
        });

        Schema::create('cpd_settings', function (Blueprint $table) {
            $table->id();
            $table->string('academic_year');
            $table->string('role')->nullable();
            $table->decimal('target_hours', 6, 1)->default(40);
            $table->decimal('term1_target', 6, 1)->default(13);
            $table->decimal('term2_target', 6, 1)->default(13);
            $table->decimal('term3_target', 6, 1)->default(14);
            $table->json('points_config')->nullable();
            $table->timestamps();
        });

        Schema::create('cpd_activity_templates', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('type', ['workshop', 'course', 'conference', 'seminar', 'peer_observation', 'self_study', 'mentoring', 'online_training', 'research', 'other'])->default('workshop');
            $table->string('provider')->nullable();
            $table->decimal('default_hours', 6, 1)->default(1);
            $table->decimal('default_points', 6, 1)->default(1);
            $table->boolean('is_mandatory')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cpd_activity_templates');
        Schema::dropIfExists('cpd_settings');

        Schema::table('cpd_activities', function (Blueprint $table) {
            $table->dropColumn(['is_mandatory', 'approval_status', 'approved_by', 'approved_at', 'approval_remarks', 'points', 'goal_id', 'observation_id']);
        });
    }
};
