<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/privacy-policy', 'privacy')->name('privacy-policy');
Route::view('/terms-of-service', 'terms')->name('terms-of-service');
Route::view('/user-data-deletion', 'data-deletion')->name('data-deletion');
