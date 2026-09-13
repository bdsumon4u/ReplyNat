<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inbox_canned_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('shortcut')->index(); // e.g. "pricing", "support" (without slash)
            $table->text('content');
            $table->timestamps();

            $table->unique(['user_id', 'shortcut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inbox_canned_responses');
    }
};
