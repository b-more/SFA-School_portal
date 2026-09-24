<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row per USSD session (dial-to-hangup). Interactions are stored
        // as a JSON array under `transcript` so a single insert per keypress
        // isn't needed — USSD is time-sensitive and a single UPDATE per hit
        // is faster.
        Schema::create('ussd_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 64)->unique();
            $table->string('msisdn', 20)->index();
            $table->foreignId('guardian_id')->nullable()->constrained('parent_guardians')->nullOnDelete();
            $table->string('shortcode', 32)->nullable();  // e.g. "388*XX"
            $table->string('state', 32)->default('START')->index();
            $table->json('data')->nullable();
            $table->json('transcript')->nullable();       // array of {ts, dir, text}
            $table->boolean('ended')->default(false)->index();
            $table->timestamp('last_input_at')->nullable();
            $table->timestamps();

            $table->index(['msisdn', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ussd_sessions');
    }
};
