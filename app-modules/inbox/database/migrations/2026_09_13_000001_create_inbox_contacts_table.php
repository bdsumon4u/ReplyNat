<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inbox_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name')->default('Visitor');
            $table->string('avatar_url')->nullable();
            $table->string('email')->nullable()->index();
            $table->string('phone')->nullable()->index();
            $table->string('platform')->index(); // 'facebook', 'instagram', 'whatsapp', etc.
            $table->string('platform_sender_id')->index(); // PSID or IGSID
            $table->json('custom_attributes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'platform', 'platform_sender_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inbox_contacts');
    }
};
