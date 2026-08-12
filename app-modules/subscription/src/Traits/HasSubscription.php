<?php

namespace Hotash\Subscription\Traits;

use Hotash\Subscription\Models\Invoice;
use Hotash\Subscription\Models\Subscription;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

trait HasSubscription
{
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'user_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'user_id');
    }

    public function activeSubscription(): ?Subscription
    {
        return $this->subscriptions()
            ->active()
            ->latest('id')
            ->first();
    }

    public function ensureDefaultSubscription(): ?Subscription
    {
        if ($this->subscriptions()->doesntExist()) {
            $plans = config('subscription.plans', []);
            $defaultPlanKey = array_key_first($plans) ?? 'pro-1-month';

            return $this->subscribeToPlan($defaultPlanKey);
        }

        return $this->activeSubscription() ?? $this->subscriptions()->latest('id')->first();
    }

    public function ensureRenewalInvoice(?Subscription $subscription = null): ?Invoice
    {
        $subscription ??= $this->activeSubscription() ?? $this->subscriptions()->latest('id')->first();

        if ($subscription && $subscription->ended() && ! $subscription->canceled()) {
            if ($subscription->status === 'active') {
                $subscription->update(['status' => 'pending']);
            }

            $unpaid = $subscription->invoice()
                ->where('status', 'unpaid')
                ->first();

            if (! $unpaid) {
                return Invoice::create([
                    'user_id' => $this->id,
                    'subscription_id' => $subscription->id,
                    'invoice_number' => Invoice::generateNextInvoiceNumber(),
                    'amount' => $subscription->price,
                    'currency' => $subscription->plan['currency'] ?? 'BDT',
                    'status' => 'unpaid',
                    'due_date' => $subscription->ends_at ?? now(),
                ]);
            }

            return $unpaid;
        }

        return null;
    }

    public function subscribeToPlan(string $planKey): Subscription
    {
        $plans = config('subscription.plans', []);
        $plan = $plans[$planKey] ?? reset($plans);
        $targetPlanKey = $plan['key'] ?? $planKey;

        // Reuse existing pending subscription with unpaid invoice for the same plan
        $existingPending = $this->subscriptions()
            ->where('plan_key', $targetPlanKey)
            ->where('status', 'pending')
            ->latest('id')
            ->first();

        if ($existingPending) {
            $hasUnpaidInvoice = $existingPending->invoice()
                ->where('status', 'unpaid')
                ->exists();

            if ($hasUnpaidInvoice) {
                return $existingPending;
            }
        }

        $hasPriorSubscriptions = $this->subscriptions()->exists();

        $trialDays = (int) config('subscription.trial_period_days', 21);

        if (! $hasPriorSubscriptions) {
            // First subscription upon registration: Grant 21-day free trial starting today
            $startsAt = Carbon::now();
            $trialEndsAt = (clone $startsAt)->addDays($trialDays);
            $status = 'active';
            $baseForEndsAt = (clone $trialEndsAt);
        } else {
            // Plan switch / renewal: Starts in pending status until invoice is paid
            $startsAt = Carbon::now();
            $trialEndsAt = null;
            $status = 'pending';
            $baseForEndsAt = (clone $startsAt);
        }

        $period = (int) ($plan['invoice_period'] ?? 1);
        $interval = $plan['invoice_interval'] ?? 'month';

        $endsAt = (clone $baseForEndsAt);
        if ($interval === 'year') {
            $endsAt->addYears($period);
        } else {
            $endsAt->addMonths($period);
        }

        $subscription = $this->subscriptions()->create([
            'plan_key' => $targetPlanKey,
            'status' => $status,
            'trial_ends_at' => $trialEndsAt,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'canceled_at' => null,
        ]);

        if ($status === 'pending') {
            Invoice::create([
                'user_id' => $this->id,
                'subscription_id' => $subscription->id,
                'invoice_number' => Invoice::generateNextInvoiceNumber(),
                'amount' => $plan['price'] ?? 0,
                'currency' => $plan['currency'] ?? 'BDT',
                'status' => 'unpaid',
                'due_date' => Carbon::now(),
            ]);
        }

        return $subscription;
    }
}
