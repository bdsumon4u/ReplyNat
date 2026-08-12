<?php

namespace Hotash\Subscription\Services;

use App\Models\User;
use Exception;
use Hotash\Subscription\Models\Invoice;
use Hotash\Subscription\Models\Subscription;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SubscriptionPaymentService
{
    /**
     * Get plan configuration by plan key.
     */
    public function getPlan(string $planKey): array
    {
        $plans = config('subscription.plans', []);

        if (! isset($plans[$planKey])) {
            throw new Exception(__('Invalid subscription plan selected.'));
        }

        return $plans[$planKey];
    }

    /**
     * Create HotashPay charge for an unpaid invoice and return payment redirect URL.
     */
    public function createPaymentCharge(Invoice $invoice): ?string
    {
        $baseUrl = rtrim(config('subscription.hotashpay.base_url', 'https://hotashpay.com'), '/');
        $apiKey = config('subscription.hotashpay.api_key', '');

        $user = $invoice->user;
        $returnUrl = route('filament.app.pages.billing');
        $callbackUrl = route('subscription.ipn');

        $postData = [
            'invoice_id' => (string) $invoice->id,
            'client_name' => $user?->name ?? 'Customer',
            'client_email' => $user?->email ?? 'customer@example.com',
            'client_phone' => $user?->phone ?? '01700000000',
            'amount' => (float) $invoice->amount,
            'metadata' => ['invoiceid' => $invoice->id],
            'redirect_url' => $returnUrl,
            'cancel_url' => $returnUrl,
            'webhook_url' => $callbackUrl,
            'return_type' => 'GET',
            'currency' => $invoice->currency ?? 'BDT',
        ];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'accept' => 'application/json',
                'Authorization' => 'Bearer '.$apiKey,
            ])->post($baseUrl.'/api/charge', $postData);

            if ($response->successful()) {
                $result = $response->json();

                if (isset($result['payment_url'])) {
                    return $result['payment_url'];
                }
            } else {
                Log::error('HotashPay Charge Failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (Exception $e) {
            Log::error('HotashPay Charge Exception', ['message' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Handle payment webhook IPN callback from payment gateway.
     */
    public function handlePaymentWebhook(array $payload): ?Subscription
    {
        $invoiceId = $payload['invoice_id'] ?? $payload['metadata']['invoiceid'] ?? null;
        $status = strtolower($payload['status'] ?? '');

        if (! $invoiceId) {
            throw new Exception('Invoice ID missing from webhook payload.');
        }

        $invoice = Invoice::find($invoiceId);
        if (! $invoice) {
            throw new Exception('Invoice not found: '.$invoiceId);
        }

        if (in_array($status, ['completed', 'success', 'paid'])) {
            $invoice->markAsPaid();

            return $invoice->subscription;
        }

        return null;
    }

    /**
     * Activate or swap subscription for user.
     */
    public function activateSubscription(User $user, string $planKey): Subscription
    {
        return $user->subscribeToPlan($planKey);
    }

    /**
     * Cancel current active or pending subscription if not ended.
     */
    public function cancelActiveSubscription(User $user): bool
    {
        $latest = $user->subscriptions()->latest('id')->first();

        if ($latest && ! $latest->ended() && ! $latest->canceled()) {
            $latest->cancel();

            return true;
        }

        return false;
    }
}
