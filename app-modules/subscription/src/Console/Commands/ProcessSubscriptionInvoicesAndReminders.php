<?php

namespace Hotash\Subscription\Console\Commands;

use Hotash\Subscription\Models\Invoice;
use Hotash\Subscription\Models\Subscription;
use Hotash\Subscription\Notifications\SubscriptionReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ProcessSubscriptionInvoicesAndReminders extends Command
{
    protected $signature = 'subscription:process-invoices-and-reminders';

    protected $description = 'Generate invoices 7 days before subscription renewal and send 7-day, 3-day, and 1-day email reminders.';

    public function handle(): int
    {
        $this->info('Processing subscription invoices and reminders...');

        $now = Carbon::now();

        // 0. Generate Invoice
        Subscription::query()
            ->whereNull('canceled_at')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', now()->addDays(7))
            ->whereDoesntHave('invoice', fn ($q) => $q->where('status', 'unpaid'))
            ->get()
            ->each(fn (Subscription $sub) => $sub->invoice()->create([
                'user_id' => $sub->user_id,
                'invoice_number' => Invoice::generateNextInvoiceNumber(),
                'amount' => $sub->price,
                'currency' => $sub->plan['currency'] ?? 'BDT',
                'due_date' => $sub->ends_at,
            ]));

        // ------------------------------------------------------------------
        // 1. 7 Days Before Expiry: Send 7-Day Email Reminder
        // ------------------------------------------------------------------
        $sevenDaysFromNow = (clone $now)->addDays(7);
        $subscriptions7Days = Subscription::query()
            ->whereNull('canceled_at')
            ->whereNotNull('ends_at')
            ->whereBetween('ends_at', [
                (clone $sevenDaysFromNow)->startOfDay(),
                (clone $sevenDaysFromNow)->endOfDay(),
            ])
            ->whereHas('invoice', fn ($q) => $q->where('status', 'unpaid')->whereNull('reminded_7_days_at'))
            ->with('user', 'invoice')
            ->get();

        foreach ($subscriptions7Days as $sub) {
            $sub->user->notify(new SubscriptionReminderNotification(7, $sub->invoice));
            $sub->invoice->update(['reminded_7_days_at' => $now]);
            $this->info("Sent 7-day reminder & invoice to {$sub->user->email} for subscription #{$sub->id}");
        }

        // ------------------------------------------------------------------
        // 2. 3 Days Before Expiry: Send 3-Day Email Reminder
        // ------------------------------------------------------------------
        $threeDaysFromNow = (clone $now)->addDays(3);
        $subscriptions3Days = Subscription::query()
            ->whereNull('canceled_at')
            ->whereNotNull('ends_at')
            ->whereBetween('ends_at', [
                (clone $threeDaysFromNow)->startOfDay(),
                (clone $threeDaysFromNow)->endOfDay(),
            ])
            ->whereHas('invoice', fn ($q) => $q->where('status', 'unpaid')->whereNull('reminded_3_days_at'))
            ->with('user', 'invoice')
            ->get();

        foreach ($subscriptions3Days as $sub) {
            $sub->user->notify(new SubscriptionReminderNotification(3, $sub->invoice));
            $sub->invoice->update(['reminded_3_days_at' => $now]);
            $this->info("Sent 3-day reminder to {$sub->user->email} for subscription #{$sub->id}");
        }

        // ------------------------------------------------------------------
        // 3. 1 Day Before Expiry: Send 1-Day Urgent Email Reminder
        // ------------------------------------------------------------------
        $oneDayFromNow = (clone $now)->addDays(1);
        $subscriptions1Day = Subscription::query()
            ->whereNull('canceled_at')
            ->whereNotNull('ends_at')
            ->whereBetween('ends_at', [
                (clone $oneDayFromNow)->startOfDay(),
                (clone $oneDayFromNow)->endOfDay(),
            ])
            ->whereHas('invoice', fn ($q) => $q->where('status', 'unpaid')->whereNull('reminded_1_day_at'))
            ->with('user', 'invoice')
            ->get();

        foreach ($subscriptions1Day as $sub) {
            $sub->user->notify(new SubscriptionReminderNotification(1, $sub->invoice));
            $sub->invoice->update(['reminded_1_day_at' => $now]);
            $this->info("Sent 1-day reminder to {$sub->user->email} for subscription #{$sub->id}");
        }

        // ------------------------------------------------------------------
        // 4. Mark Expired Subscriptions & Ensure Renewal Invoices
        // ------------------------------------------------------------------
        $expiredSubscriptions = Subscription::query()
            ->whereNull('canceled_at')
            ->where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', $now)
            ->with('user')
            ->get();

        foreach ($expiredSubscriptions as $sub) {
            if ($sub->user) {
                $sub->user->ensureRenewalInvoice($sub);
                $this->info("Subscription #{$sub->id} transitioned to pending with renewal invoice created.");
            }
        }

        $this->info('Completed processing invoices and reminders.');

        return Command::SUCCESS;
    }
}
