<?php

namespace Hotash\Subscription\Filament\Widgets;

use Filament\Widgets\Widget;
use Hotash\N8n\Models\N8nWorkflow;
use Illuminate\Support\Facades\Auth;

class N8nWidget extends Widget
{
    protected string $view = 'subscription::filament.widgets.n8n-widget';

    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = -4;

    public function getViewData(): array
    {
        $user = Auth::user();
        $n8nWorkflow = $user ? N8nWorkflow::where('user_id', $user->id)->first() : null;

        return [
            'n8nWorkflow' => $n8nWorkflow,
        ];
    }
}
