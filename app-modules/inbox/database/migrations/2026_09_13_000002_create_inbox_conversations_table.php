<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inbox_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_id')->constrained('inbox_contacts')->cascadeOnDelete();
            $table->foreignId('connected_asset_id')->nullable()->constrained('connected_assets')->nullOnDelete();
            $table->string('channel')->default('facebook_page')->index(); // 'facebook_page', 'instagram', 'whatsapp'
            $table->string('status')->default('open')->index(); // 'open', 'pending', 'snoozed', 'closed'
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('last_message_preview')->nullable();
            $table->timestamp('last_message_at')->nullable()->index();
            $table->timestamp('last_incoming_at')->nullable();
            $table->unsignedInteger('unread_count')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'channel']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inbox_conversations');
    }
};
