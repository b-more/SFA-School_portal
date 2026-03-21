<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('message_broadcasts', function (Blueprint $table) {
            $table->string('recipient_scope')->default('parents')->after('filters');
            // parents, all_staff, teachers, specific_grade, specific_section
        });

        Schema::create('broadcast_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_broadcast_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('read_at');
            $table->unique(['message_broadcast_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcast_reads');

        Schema::table('message_broadcasts', function (Blueprint $table) {
            $table->dropColumn('recipient_scope');
        });
    }
};
