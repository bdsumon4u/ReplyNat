<?php

use Hotash\Inbox\Http\Controllers\MetaWebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/inbox')->group(function () {
    Route::get('webhooks/meta', [MetaWebhookController::class, 'verify'])->name('inbox.webhook.verify');
    Route::post('webhooks/meta', [MetaWebhookController::class, 'handle'])->name('inbox.webhook.handle');
});
