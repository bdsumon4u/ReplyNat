<?php

use Hotash\Chatwoot\Models\ChatwootAccount;
use Hotash\Chatwoot\Services\ChatwootService;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/chatwoot/sso', function (ChatwootService $chatwoot) {
        $user = auth()->user();
        $accountMapping = ChatwootAccount::where('user_id', $user->id)->first();

        if (! $accountMapping || ! $accountMapping->chatwoot_user_id) {
            return back()->with('error', 'Chatwoot account not found or not synced yet.');
        }

        if ($accountMapping->status !== 'active') {
            return back()->with('error', 'Your Chatwoot account is currently suspended.');
        }

        $loginUrl = $chatwoot->getLoginUrl($accountMapping->chatwoot_user_id);

        if (! $loginUrl) {
            return back()->with('error', 'Failed to generate SSO login link. Please try again.');
        }

        return redirect()->away($loginUrl);
    })->name('chatwoot.sso');
});
