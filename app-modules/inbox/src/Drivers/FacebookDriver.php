<?php

namespace Hotash\Inbox\Drivers;

use Hotash\Inbox\Contracts\ChannelDriverInterface;
use Hotash\Inbox\Models\Conversation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookDriver implements ChannelDriverInterface
{
    protected string $graphVersion;

    public function __construct()
    {
        $this->graphVersion = (string) config('comments.graph_version', 'v20.0');
    }

    public function sendMessage(Conversation $conversation, string $text, array $attachments = []): array
    {
        $asset = $conversation->connectedAsset;
        $contact = $conversation->contact;

        if (! $asset || ! $asset->access_token) {
            return [
                'success' => false,
                'error' => 'No access token available for this Facebook Page.',
            ];
        }

        $recipientId = $contact->platform_sender_id;
        $accessToken = $asset->access_token;
        $url = "https://graph.facebook.com/{$this->graphVersion}/me/messages";

        try {
            $lastMessageId = null;

            // 1. Send text if provided
            if (trim($text) !== '') {
                $response = Http::withToken($accessToken)
                    ->post($url, [
                        'recipient' => ['id' => $recipientId],
                        'messaging_type' => 'RESPONSE',
                        'message' => ['text' => $text],
                    ]);

                if ($response->failed()) {
                    Log::error('FacebookDriver: Failed to send text message', [
                        'response' => $response->json(),
                        'conversation_id' => $conversation->id,
                    ]);

                    return [
                        'success' => false,
                        'error' => $response->json('error.message', 'Failed to send message via Facebook.'),
                    ];
                }

                $lastMessageId = $response->json('message_id');
            }

            // 2. Send attachments if provided
            foreach ($attachments as $att) {
                $attUrl = $att['url'] ?? null;
                $attType = $att['type'] ?? 'image';

                if (! $attUrl) {
                    continue;
                }

                $response = Http::withToken($accessToken)
                    ->post($url, [
                        'recipient' => ['id' => $recipientId],
                        'messaging_type' => 'RESPONSE',
                        'message' => [
                            'attachment' => [
                                'type' => in_array($attType, ['image', 'video', 'audio', 'file']) ? $attType : 'file',
                                'payload' => [
                                    'url' => $attUrl,
                                    'is_reusable' => true,
                                ],
                            ],
                        ],
                    ]);

                if ($response->successful()) {
                    $lastMessageId = $response->json('message_id') ?? $lastMessageId;
                }
            }

            return [
                'success' => true,
                'message_id' => $lastMessageId,
            ];
        } catch (\Throwable $e) {
            Log::error('FacebookDriver: Exception while sending message', [
                'error' => $e->getMessage(),
                'conversation_id' => $conversation->id,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function fetchContactProfile(string $platformSenderId, string $accessToken): ?array
    {
        try {
            $response = Http::get("https://graph.facebook.com/{$this->graphVersion}/{$platformSenderId}", [
                'fields' => 'first_name,last_name,profile_pic',
                'access_token' => $accessToken,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $firstName = $data['first_name'] ?? '';
                $lastName = $data['last_name'] ?? '';
                $fullName = trim("{$firstName} {$lastName}") ?: 'Facebook User';

                return [
                    'name' => $fullName,
                    'avatar_url' => $data['profile_pic'] ?? null,
                ];
            }
        } catch (\Throwable $e) {
            Log::warning('FacebookDriver: Could not fetch user profile', [
                'sender_id' => $platformSenderId,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }
}
