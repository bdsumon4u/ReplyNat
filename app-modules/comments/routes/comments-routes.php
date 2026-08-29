<?php

use Hotash\Comments\Http\Controllers\CommentsWebhookController;
use Hotash\Comments\Http\Controllers\MetaOAuthController;
use Illuminate\Support\Facades\Route;

// OAuth 1-Click Connection Routes
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/comments/auth/facebook', [MetaOAuthController::class, 'redirect'])
        ->name('comments.auth.redirect');
    Route::get('/comments/auth/callback', [MetaOAuthController::class, 'callback'])
        ->name('comments.auth.callback');
});

// Meta Webhook Verification & Event Ingestion
Route::get('/api/comments/webhook', [CommentsWebhookController::class, 'verify'])
    ->name('comments.webhook.verify');
Route::post('/api/comments/webhook', [CommentsWebhookController::class, 'handle'])
    ->name('comments.webhook.handle');
