<?php

namespace Hotash\Subscription\Filament\Widgets;

use Filament\Widgets\Widget;
use Hotash\Subscription\Models\Invoice;
use Illuminate\Support\Facades\Auth;

class CurrentPlanWidget extends Widget
{
    protected string $view = 'subscription::filament.widgets.current-plan-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = -10;

    public function getViewData(): array
    {
        $user = Auth::user();
        $subscription = $user?->ensureDefaultSubscription();

        if ($user && $subscription) {
            $user->ensureRenewalInvoice($subscription);
        }

        $plan = $subscription?->plan;
        $isTrial = $subscription?->onTrial() ?? false;
        $isCanceled = $subscription?->canceled() ?? false;
        $isPending = $subscription?->isPendingPayment() ?? false;
        $isExpired = $subscription ? ($subscription->ended() && ! $isTrial) : false;
        $isActive = $subscription?->active() ?? false;

        $endsAt = $isTrial ? $subscription?->trial_ends_at : $subscription?->ends_at;

        $daysRemaining = null;
        if ($endsAt) {
            $daysRemaining = (int) max(0, now()->diffInDays($endsAt, false));
        }

        $unpaidInvoice = $user ? Invoice::where('user_id', $user->id)->where('status', 'unpaid')->latest('id')->first() : null;

        return [
            'subscription' => $subscription,
            'plan' => $plan,
            'isTrial' => $isTrial,
            'isCanceled' => $isCanceled,
            'isPending' => $isPending,
            'isExpired' => $isExpired,
            'isActive' => $isActive,
            'endsAt' => $endsAt,
            'daysRemaining' => $daysRemaining,
            'unpaidInvoice' => $unpaidInvoice,
        ];
    }
}
