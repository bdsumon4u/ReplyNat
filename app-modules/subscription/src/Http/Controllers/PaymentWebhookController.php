<?php

namespace Hotash\Subscription\Http\Controllers;

use App\Http\Controllers\Controller;
use Hotash\Subscription\Services\SubscriptionPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class PaymentWebhookController extends Controller
{
    /**
     * Handle payment gateway Webhook / IPN notification.
     */
    public function handleIPN(Request $request, SubscriptionPaymentService $paymentService): JsonResponse
    {
        try {
            $payload = $request->all();

            $subscription = $paymentService->handlePaymentWebhook($payload);

            return response()->json([
                'status' => 'success',
                'message' => 'Payment processed and subscription activated successfully.',
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
