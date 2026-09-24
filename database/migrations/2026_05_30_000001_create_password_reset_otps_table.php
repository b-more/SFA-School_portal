<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('password_reset_otps')) {
            Schema::create('password_reset_otps', function (Blueprint $table) {
                $table->id();
                $table->string('phone', 20)->index();   // normalized E.164
                $table->string('code_hash', 255);        // hash of 6-digit OTP, never the plaintext
                $table->unsignedTinyInteger('attempts')->default(0);
                $table->timestamp('expires_at');
                $table->timestamps();
                $table->index(['phone', 'expires_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_otps');
    }
};
