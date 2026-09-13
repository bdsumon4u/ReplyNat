<?php

namespace Hotash\Inbox\Jobs;

use Hotash\Comments\Models\ConnectedAsset;
use Hotash\Inbox\Models\Contact;
use Hotash\Inbox\Models\Conversation;
use Hotash\Inbox\Models\Message;
use Hotash\Inbox\Services\ChannelManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessMetaWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public array $payload
    ) {}

    public function handle(ChannelManager $channelManager): void
    {
        $object = $this->payload['object'] ?? null;
        $entries = $this->payload['entry'] ?? [];

        foreach ($entries as $entry) {
            $assetId = (string) ($entry['id'] ?? '');
            $messagingEvents = $entry['messaging'] ?? [];

            // Find matching asset
            $asset = ConnectedAsset::where('asset_id', $assetId)
                ->orWhere('parent_asset_id', $assetId)
                ->first();

            if (! $asset) {
                Log::warning('ProcessMetaWebhookJob: No ConnectedAsset found for ID', ['asset_id' => $assetId]);

                continue;
            }

            foreach ($messagingEvents as $event) {
                $this->processMessagingEvent($event, $asset, $channelManager);
            }
        }
    }

    protected function processMessagingEvent(array $event, ConnectedAsset $asset, ChannelManager $channelManager): void
    {
        $senderId = (string) ($event['sender']['id'] ?? '');
        $recipientId = (string) ($event['recipient']['id'] ?? '');

        // 1. Handle incoming message
        if (isset($event['message'])) {
            $msgData = $event['message'];
            $mid = $msgData['mid'] ?? null;
            $isEcho = (bool) ($msgData['is_echo'] ?? false);

            if ($isEcho) {
                // Outgoing message sent from native Meta app or other integration
                $this->handleEchoMessage($event, $asset, $senderId, $recipientId, $msgData, $mid);

                return;
            }

            // Ignore messages from the page/account itself
            if ($senderId === $asset->asset_id) {
                return;
            }

            $platform = $asset->isInstagram() ? 'instagram' : 'facebook';
            $channel = $asset->isInstagram() ? 'instagram' : 'facebook_page';

            // Find or create Contact
            $contact = Contact::firstOrCreate(
                [
                    'user_id' => $asset->user_id,
                    'platform' => $platform,
                    'platform_sender_id' => $senderId,
                ],
                [
                    'name' => $asset->isInstagram() ? 'Instagram User' : 'Facebook User',
                ]
            );

            // Fetch profile if name is generic
            if (in_array($contact->name, ['Facebook User', 'Instagram User', 'Visitor', '']) && $asset->access_token) {
                $profile = $channelManager->driver($channel)->fetchContactProfile($senderId, $asset->access_token);
                if ($profile) {
                    $contact->update([
                        'name' => $profile['name'] ?? $contact->name,
                        'avatar_url' => $profile['avatar_url'] ?? $contact->avatar_url,
                    ]);
                }
            }

            // Find or create Conversation
            $conversation = Conversation::firstOrCreate(
                [
                    'user_id' => $asset->user_id,
                    'contact_id' => $contact->id,
                    'connected_asset_id' => $asset->id,
                ],
                [
                    'channel' => $channel,
                    'status' => 'open',
                ]
            );

            // Re-open if closed
            if ($conversation->status === 'closed') {
                $conversation->status = 'open';
            }

            // Parse text and attachments
            $text = $msgData['text'] ?? '';
            $attachments = [];
            $messageType = 'text';

            if (! empty($msgData['attachments'])) {
                foreach ($msgData['attachments'] as $att) {
                    $attType = $att['type'] ?? 'file';
                    $attUrl = $att['payload']['url'] ?? null;
                    if ($attUrl) {
                        $attachments[] = [
                            'type' => $attType,
                            'url' => $attUrl,
                        ];
                    }
                }
                if (empty($text) && ! empty($attachments)) {
                    $messageType = $attachments[0]['type'] ?? 'image';
                    $text = '['.ucfirst($messageType).']';
                }
            }

            // Create Message record if not already recorded
            if ($mid) {
                $existing = Message::where('platform_message_id', $mid)->first();
                if ($existing) {
                    return;
                }
            }

            Message::create([
                'conversation_id' => $conversation->id,
                'sender_type' => 'contact',
                'message_type' => $messageType,
                'content' => $msgData['text'] ?? (count($attachments) ? '['.ucfirst($attachments[0]['type']).']' : ''),
                'attachments' => count($attachments) ? $attachments : null,
                'status' => 'delivered',
                'platform_message_id' => $mid,
                'is_private_note' => false,
                'raw_payload' => $event,
            ]);

            // Update conversation stats
            $conversation->update([
                'last_message_preview' => $text,
                'last_message_at' => now(),
                'last_incoming_at' => now(),
                'unread_count' => $conversation->unread_count + 1,
            ]);
        }

        // 2. Handle Read receipts
        if (isset($event['read'])) {
            $this->handleReadReceipt($event, $asset, $senderId);
        }

        // 3. Handle Delivery receipts
        if (isset($event['delivery'])) {
            $this->handleDeliveryReceipt($event, $asset, $senderId);
        }
    }

    protected function handleEchoMessage(array $event, ConnectedAsset $asset, string $pageId, string $recipientId, array $msgData, ?string $mid): void
    {
        $contact = Contact::where('user_id', $asset->user_id)
            ->where('platform_sender_id', $recipientId)
            ->first();

        if (! $contact) {
            return;
        }

        $conversation = Conversation::where('contact_id', $contact->id)
            ->where('connected_asset_id', $asset->id)
            ->first();

        if (! $conversation) {
            return;
        }

        if ($mid && Message::where('platform_message_id', $mid)->exists()) {
            return;
        }

        $text = $msgData['text'] ?? '';
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'agent',
            'message_type' => 'text',
            'content' => $text,
            'status' => 'sent',
            'platform_message_id' => $mid,
            'is_private_note' => false,
            'raw_payload' => $event,
        ]);

        $conversation->update([
            'last_message_preview' => $text,
            'last_message_at' => now(),
        ]);
    }

    protected function handleReadReceipt(array $event, ConnectedAsset $asset, string $senderId): void
    {
        $contact = Contact::where('user_id', $asset->user_id)
            ->where('platform_sender_id', $senderId)
            ->first();

        if ($contact) {
            Message::whereHas('conversation', fn ($q) => $q->where('contact_id', $contact->id))
                ->where('sender_type', 'agent')
                ->where('status', '!=', 'read')
                ->update(['status' => 'read']);
        }
    }

    protected function handleDeliveryReceipt(array $event, ConnectedAsset $asset, string $senderId): void
    {
        $watermark = $event['delivery']['watermark'] ?? null;
        $mids = $event['delivery']['mids'] ?? [];

        if (! empty($mids)) {
            Message::whereIn('platform_message_id', $mids)
                ->where('status', 'sent')
                ->update(['status' => 'delivered']);
        }
    }
}
