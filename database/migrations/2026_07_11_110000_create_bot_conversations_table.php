<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bot_conversations', function (Blueprint $table) {
            $table->id();
            // Phone stored digits-only (2609…) so lookups are index-friendly
            // and match ParentGuardian::normalisePhoneToInternational output.
            $table->string('phone', 20)->index();
            // 'in' = user → bot; 'out' = bot → user.
            $table->enum('direction', ['in', 'out'])->index();
            // Free-form kind so we can label rich interactive types cleanly:
            // text, interactive_list, interactive_button, image, document,
            // template, list_reply, button_reply.
            $table->string('kind', 32);
            // The visible content or a preview (button/row title for reply events).
            $table->text('content')->nullable();
            // Structured payload — button ids, list rows, media info, meta.
            $table->json('meta')->nullable();
            // The bot's flow-machine state at the time the event happened.
            $table->string('flow_state', 32)->nullable();
            // Guardian we resolved this phone to (if any).
            $table->foreignId('guardian_id')->nullable()->constrained('parent_guardians')->nullOnDelete();
            // Meta wamid (inbound) or Meta-returned message id (outbound), for dedupe.
            $table->string('message_id', 128)->nullable();
            $table->timestamps();

            $table->index(['phone', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bot_conversations');
    }
};
