<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('connected_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('platform'); // 'facebook_page' or 'instagram_account'
            $table->string('asset_id'); // Facebook Page ID or Instagram Business Account ID
            $table->string('asset_name');
            $table->string('username')->nullable();
            $table->text('avatar_url')->nullable();
            $table->string('parent_asset_id')->nullable(); // Facebook Page ID for linked IG
            $table->text('access_token'); // Long-lived Page Access Token
            $table->boolean('is_active')->default(true);
            $table->boolean('auto_reply_enabled')->default(true);
            $table->boolean('private_reply_enabled')->default(false);
            $table->json('settings')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'platform', 'asset_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('connected_assets');
    }
};
