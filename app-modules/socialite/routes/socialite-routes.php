<?php

use Hotash\Socialite\Http\Controllers\SocialLoginController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::get('/auth/{provider}/redirect', [SocialLoginController::class, 'redirect'])->name('socialite.redirect');
    Route::get('/auth/{provider}/callback', [SocialLoginController::class, 'callback'])->name('socialite.callback');
});
