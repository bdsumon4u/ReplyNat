<?php

namespace Hotash\Comments\Http\Controllers;

use Hotash\Comments\Models\ConnectedAsset;
use Hotash\Comments\Services\MetaGraphService;
use Hotash\N8n\Models\N8nWorkflow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CommentsWebhookController extends Controller
{
    /**
     * Handle Meta Webhook Verification Handshake.
     */
    public function verify(Request $request): Response|JsonResponse
    {
        $mode = $request->query('hub_mode') ?? $request->query('hub.mode');
        $token = $request->query('hub_verify_token') ?? $request->query('hub.verify_token');
        $challenge = $request->query('hub_challenge') ?? $request->query('hub.challenge');

        $expectedToken = config('comments.webhook_verify_token');

        if ($mode === 'subscribe' && $token === $expectedToken) {
            Log::info('CommentsWebhookController: Meta Webhook handshake verified successfully.');

            return response((string) $challenge, 200)
                ->header('Content-Type', 'text/plain');
        }

        Log::warning('CommentsWebhookController: Verification failed', [
            'mode' => $mode,
            'token' => $token,
        ]);

        return response()->json(['error' => 'Forbidden'], 403);
    }

    /**
     * Ingest Real-Time Events from Facebook and Instagram (Relaying DMs to Chatwoot and Comments to n8n).
     */
    public function handle(Request $request, MetaGraphService $meta): JsonResponse
    {
        $payload = $request->all();
        $object = $payload['object'] ?? null;
        $entries = $payload['entry'] ?? [];

        Log::info('CommentsWebhookController: Incoming Meta webhook', [
            'object' => $object,
            'entries_count' => count($entries),
        ]);

        $hasMessagingEvents = false;

        foreach ($entries as $entry) {
            $assetId = (string) ($entry['id'] ?? '');
            $changes = $entry['changes'] ?? [];
            $messaging = $entry['messaging'] ?? $entry['standby'] ?? [];

            // Check if this entry contains direct messages (Messenger / Instagram DMs)
            if (! empty($messaging)) {
                $hasMessagingEvents = true;
            }

            foreach ($changes as $change) {
                $field = $change['field'] ?? '';
                $value = $change['value'] ?? [];

                if (in_array($field, ['messages', 'messaging_postbacks', 'message_deliveries', 'message_reads'])) {
                    $hasMessagingEvents = true;
                }

                // 1. Facebook Post Comment
                if ($object === 'page' && $field === 'feed') {
                    $item = $value['item'] ?? '';
                    $verb = $value['verb'] ?? '';

                    if ($item === 'comment' && in_array($verb, ['add', 'created', ''])) {
                        $this->processFacebookComment($assetId, $value);
                    }
                }

                // 2. Instagram Post/Reel Comment
                if ($object === 'instagram' && $field === 'comments') {
                    $this->processInstagramComment($assetId, $value);
                }
            }
        }

        // If direct messaging events are present, relay to Chatwoot bot endpoint
        if ($hasMessagingEvents) {
            $this->relayToChatwoot($request);
        }

        return response()->json(['status' => 'EVENT_RECEIVED'], 200);
    }

    /**
     * Relay Direct Messaging event to Chatwoot bot endpoint.
     */
    protected function relayToChatwoot(Request $request): void
    {
        try {
            $chatwootUrl = config('chatwoot.url');
            if (empty($chatwootUrl)) {
                return;
            }

            $chatwootBotUrl = rtrim($chatwootUrl, '/').'/bot';

            $headers = [];
            if ($sig = $request->header('X-Hub-Signature-256')) {
                $headers['X-Hub-Signature-256'] = $sig;
            }
            if ($sigSha1 = $request->header('X-Hub-Signature')) {
                $headers['X-Hub-Signature'] = $sigSha1;
            }

            Http::withHeaders($headers)
                ->timeout(8)
                ->post($chatwootBotUrl, $request->all());

            Log::info('CommentsWebhookController: Relayed DM event to Chatwoot');
        } catch (\Throwable $e) {
            Log::error('CommentsWebhookController: Failed to relay DM to Chatwoot', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Process Facebook Comment and forward to n8n AI workflow.
     */
    protected function processFacebookComment(string $pageId, array $data): void
    {
        $commentId = $data['comment_id'] ?? null;
        $message = $data['message'] ?? '';
        $postId = $data['post_id'] ?? null;
        $senderId = $data['from']['id'] ?? null;
        $senderName = $data['from']['name'] ?? 'User';

        // Ignore comments posted by the page itself to avoid echo loops
        if ($senderId === $pageId || empty($commentId) || empty($message)) {
            return;
        }

        $asset = ConnectedAsset::where('platform', 'facebook_page')
            ->where('asset_id', $pageId)
            ->where('is_active', true)
            ->first();

        if (! $asset) {
            Log::info("CommentsWebhook: No active asset found for Facebook Page #{$pageId}");

            return;
        }

        $this->forwardToN8n($asset, [
            'platform' => 'facebook',
            'asset_id' => $pageId,
            'asset_name' => $asset->asset_name,
            'comment_id' => $commentId,
            'post_id' => $postId,
            'sender_id' => $senderId,
            'sender_name' => $senderName,
            'comment_text' => $message,
            'page_access_token' => $asset->access_token,
            'auto_reply' => $asset->auto_reply_enabled,
            'private_reply' => $asset->private_reply_enabled,
            'settings' => $asset->settings ?? [],
        ]);
    }

    /**
     * Process Instagram Comment and forward to n8n AI workflow.
     */
    protected function processInstagramComment(string $igId, array $data): void
    {
        $commentId = $data['id'] ?? null;
        $text = $data['text'] ?? '';
        $mediaId = $data['media']['id'] ?? null;
        $senderId = $data['from']['id'] ?? null;
        $senderUsername = $data['from']['username'] ?? 'User';

        // Ignore comments posted by the IG account itself
        if ($senderId === $igId || empty($commentId) || empty($text)) {
            return;
        }

        $asset = ConnectedAsset::where('platform', 'instagram_account')
            ->where('asset_id', $igId)
            ->where('is_active', true)
            ->first();

        if (! $asset) {
            Log::info("CommentsWebhook: No active asset found for Instagram Account #{$igId}");

            return;
        }

        $this->forwardToN8n($asset, [
            'platform' => 'instagram',
            'asset_id' => $igId,
            'asset_name' => $asset->asset_name,
            'comment_id' => $commentId,
            'media_id' => $mediaId,
            'sender_id' => $senderId,
            'sender_username' => $senderUsername,
            'comment_text' => $text,
            'page_access_token' => $asset->access_token,
            'auto_reply' => $asset->auto_reply_enabled,
            'private_reply' => $asset->private_reply_enabled,
            'settings' => $asset->settings ?? [],
        ]);
    }

    /**
     * Forward comment event to user's n8n comment automation workflow.
     */
    protected function forwardToN8n(ConnectedAsset $asset, array $payload): void
    {
        try {
            $user = $asset->user;
            if (! $user) {
                return;
            }

            // Find user's active n8n workflow
            $workflow = N8nWorkflow::where('user_id', $user->id)->first();
            $webhookUrl = config('n8n.webhook_url') ?: null;

            if ($workflow && ! empty($workflow->settings['comment_webhook_url'])) {
                $webhookUrl = $workflow->settings['comment_webhook_url'];
            }

            if ($webhookUrl) {
                Http::timeout(5)
                    ->post($webhookUrl, $payload);
            }
        } catch (\Throwable $e) {
            Log::error('CommentsWebhook: Failed to forward comment to n8n', [
                'asset_id' => $asset->asset_id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
