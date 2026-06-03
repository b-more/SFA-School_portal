<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fcm_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->enum('app', ['parent', 'teacher'])->index();
            $table->string('token', 4000)->unique();
            $table->string('device_label')->nullable();   // e.g. "Samsung A52, Android 14"
            $table->dateTime('last_active_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fcm_tokens');
    }
};
