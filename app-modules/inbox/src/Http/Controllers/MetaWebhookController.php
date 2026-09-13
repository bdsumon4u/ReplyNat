<?php

namespace Hotash\Inbox\Http\Controllers;

use Hotash\Inbox\Jobs\ProcessMetaWebhookJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class MetaWebhookController extends Controller
{
    /**
     * Webhook Handshake / Verification (hub.challenge)
     */
    public function verify(Request $request): Response|JsonResponse
    {
        $mode = $request->query('hub_mode');
        $verifyToken = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        $expectedToken = config('inbox.verify_token') ?: config('comments.verify_token', 'replynat_meta_webhook_secret');

        if ($mode === 'subscribe' && $verifyToken === $expectedToken) {
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        Log::warning('Meta Webhook verification failed', [
            'mode' => $mode,
            'received_token' => $verifyToken,
        ]);

        return response()->json(['error' => 'Forbidden'], 403);
    }

    /**
     * Handle incoming Meta Webhook events (Messages, Reads, Deliveries)
     */
    public function handle(Request $request): JsonResponse
    {
        $signature = $request->header('X-Hub-Signature-256');
        $appSecret = config('inbox.app_secret') ?: config('comments.app_secret');

        if ($appSecret && $signature) {
            $expectedSignature = 'sha256='.hash_hmac('sha256', $request->getContent(), $appSecret);
            if (! hash_equals($expectedSignature, $signature)) {
                Log::warning('Meta Webhook signature mismatch');

                return response()->json(['error' => 'Invalid signature'], 403);
            }
        }

        $payload = $request->all();

        // Process asynchronously
        ProcessMetaWebhookJob::dispatch($payload);

        return response()->json(['status' => 'EVENT_RECEIVED'], 200);
    }
}
