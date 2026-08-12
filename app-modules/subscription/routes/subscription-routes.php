<?php

use Hotash\Subscription\Http\Controllers\PaymentWebhookController;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Facades\Route;

Route::post('/subscription/ipn', [PaymentWebhookController::class, 'handleIPN'])
    ->withoutMiddleware([PreventRequestForgery::class])
    ->name('subscription.ipn');
