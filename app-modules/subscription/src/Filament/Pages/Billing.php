<?php

namespace Hotash\Subscription\Filament\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Hotash\Subscription\Models\Invoice;
use Hotash\Subscription\Services\SubscriptionPaymentService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Throwable;

class Billing extends Page
{
    use WithPagination;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?string $navigationLabel = 'Billing & Subscription';

    protected static ?string $title = 'Billing & Subscription';

    protected static ?string $slug = 'billing';

    protected string $view = 'subscription::filament.pages.billing';

    public ?string $selectedPlanKey = null;

    public string $paymentMethod = 'card';

    public bool $showCheckoutModal = false;

    public function selectPlan(string $planKey): void
    {
        $this->selectedPlanKey = $planKey;
        $this->showCheckoutModal = true;
    }

    public function closeCheckoutModal(): void
    {
        $this->showCheckoutModal = false;
        $this->selectedPlanKey = null;
    }

    public function confirmSubscription(SubscriptionPaymentService $service): mixed
    {
        if (! $this->selectedPlanKey) {
            return null;
        }

        $user = Auth::user();

        if (! $user) {
            Notification::make()
                ->title('Error')
                ->body('User not authenticated.')
                ->danger()
                ->send();

            return null;
        }

        try {
            $subscription = $service->activateSubscription($user, $this->selectedPlanKey);

            $this->closeCheckoutModal();

            $invoice = Invoice::where('subscription_id', $subscription->id)
                ->where('status', 'unpaid')
                ->latest('id')
                ->first();

            if ($invoice) {
                $paymentUrl = $service->createPaymentCharge($invoice);

                if ($paymentUrl) {
                    return redirect()->away($paymentUrl);
                }
            }

            Notification::make()
                ->title('Invoice Generated')
                ->body('Your subscription invoice has been created. Please complete the payment to activate your plan.')
                ->info()
                ->send();
        } catch (Throwable $e) {
            Notification::make()
                ->title('Subscription Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }

        return null;
    }

    public function payInvoice(int $invoiceId, SubscriptionPaymentService $service): mixed
    {
        $user = Auth::user();
        $invoice = Invoice::where('user_id', $user?->id)->find($invoiceId);

        if (! $invoice) {
            return null;
        }

        $paymentUrl = $service->createPaymentCharge($invoice);

        if ($paymentUrl) {
            return redirect()->away($paymentUrl);
        }

        Notification::make()
            ->title('Payment Connection Failed')
            ->body('Could not connect to HotashPay payment gateway. Please check your HotashPay API configuration.')
            ->danger()
            ->send();

        return null;
    }

    public function cancelSubscription(SubscriptionPaymentService $service): void
    {
        $user = Auth::user();

        if ($user && $service->cancelActiveSubscription($user)) {
            Notification::make()
                ->title('Subscription Canceled')
                ->body('Your subscription access has been canceled.')
                ->warning()
                ->send();
        } else {
            Notification::make()
                ->title('Notice')
                ->body('No active subscription found to cancel.')
                ->info()
                ->send();
        }
    }

    public function getViewData(): array
    {
        $user = Auth::user();
        $currentSubscription = $user?->ensureDefaultSubscription();

        if ($user && $currentSubscription) {
            $user->ensureRenewalInvoice($currentSubscription);
        }

        $currentPlan = $currentSubscription?->plan;
        $plans = config('subscription.plans', []);
        $features = config('subscription.features', []);
        $subscriptions = $user ? $user->subscriptions()->with('invoice')->latest('id')->paginate(10) : new LengthAwarePaginator([], 0, 10);

        $selectedPlan = $this->selectedPlanKey ? ($plans[$this->selectedPlanKey] ?? null) : null;

        $isTrial = $currentSubscription?->onTrial() ?? false;
        $isCanceled = $currentSubscription?->canceled() ?? false;
        $isPending = $currentSubscription?->isPendingPayment() ?? false;
        $isExpired = $currentSubscription ? ($currentSubscription->ended() && ! $isTrial) : false;
        $isActive = $currentSubscription?->active() ?? false;

        $effectiveEndsAt = $isTrial ? $currentSubscription?->trial_ends_at : $currentSubscription?->ends_at;
        $daysRemaining = null;
        if ($effectiveEndsAt) {
            $diff = (int) now()->diffInDays($effectiveEndsAt, false);
            $daysRemaining = max(0, $diff);
        }

        $unpaidInvoice = $user ? Invoice::where('user_id', $user->id)->where('status', 'unpaid')->latest('id')->first() : null;

        return [
            'currentSubscription' => $currentSubscription,
            'currentPlan' => $currentPlan,
            'plans' => $plans,
            'features' => $features,
            'subscriptions' => $subscriptions,
            'selectedPlan' => $selectedPlan,
            'effectiveEndsAt' => $effectiveEndsAt,
            'daysRemaining' => $daysRemaining,
            'isTrial' => $isTrial,
            'isCanceled' => $isCanceled,
            'isPending' => $isPending,
            'isExpired' => $isExpired,
            'isActive' => $isActive,
            'unpaidInvoice' => $unpaidInvoice,
        ];
    }
}
