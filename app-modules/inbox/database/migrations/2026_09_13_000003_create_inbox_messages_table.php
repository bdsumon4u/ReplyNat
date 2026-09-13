<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inbox_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('inbox_conversations')->cascadeOnDelete();
            $table->string('sender_type')->default('contact')->index(); // 'contact', 'agent', 'bot', 'system'
            $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('message_type')->default('text')->index(); // 'text', 'image', 'audio', 'video', 'file', 'template', 'private_note'
            $table->text('content')->nullable();
            $table->json('attachments')->nullable();
            $table->string('status')->default('sent')->index(); // 'pending', 'sent', 'delivered', 'read', 'failed'
            $table->string('platform_message_id')->nullable()->index(); // Meta MID
            $table->boolean('is_private_note')->default(false)->index();
            $table->json('raw_payload')->nullable();
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inbox_messages');
    }
};
