<?php

use Filament\Notifications\Notification;
use Hotash\Chatwoot\Models\ChatwootAccount;
use Hotash\Chatwoot\Services\ChatwootService;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/chatwoot/sso', function (ChatwootService $chatwoot) {
        $user = auth()->user();
        $accountMapping = ChatwootAccount::where('user_id', $user->id)->first();

        if (! $accountMapping || ! $accountMapping->chatwoot_user_id) {
            Notification::make()
                ->title('Chatwoot Not Found')
                ->body('Chatwoot account not found or not synced yet.')
                ->danger()
                ->send();

            return back();
        }

        if ($accountMapping->status !== 'active') {
            Notification::make()
                ->title('Account Suspended')
                ->body('Your Chatwoot account is currently suspended.')
                ->danger()
                ->send();

            return back();
        }

        $loginUrl = $chatwoot->getLoginUrl($accountMapping->chatwoot_user_id);

        if (! $loginUrl) {
            Notification::make()
                ->title('Chatwoot Server Unreachable')
                ->body('Could not connect to Chatwoot server. Please check if your Chatwoot container is running on Easypanel.')
                ->danger()
                ->send();

            return back();
        }

        return redirect()->away($loginUrl);
    })->name('chatwoot.sso');
});
