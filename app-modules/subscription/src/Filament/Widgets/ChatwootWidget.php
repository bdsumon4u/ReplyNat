<?php

namespace Hotash\Subscription\Filament\Widgets;

use Filament\Widgets\Widget;
use Hotash\Chatwoot\Models\ChatwootAccount;
use Illuminate\Support\Facades\Auth;

class ChatwootWidget extends Widget
{
    protected string $view = 'subscription::filament.widgets.chatwoot-widget';

    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = -5;

    public function getViewData(): array
    {
        $user = Auth::user();
        $chatwootAccount = $user ? ChatwootAccount::where('user_id', $user->id)->first() : null;

        return [
            'chatwootAccount' => $chatwootAccount,
        ];
    }
}
