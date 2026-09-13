<?php

namespace Hotash\Inbox\Drivers;

use Hotash\Inbox\Contracts\ChannelDriverInterface;
use Hotash\Inbox\Models\Conversation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InstagramDriver implements ChannelDriverInterface
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
                'error' => 'No access token available for this Instagram Account.',
            ];
        }

        $recipientId = $contact->platform_sender_id;
        $accessToken = $asset->access_token;
        $url = "https://graph.facebook.com/{$this->graphVersion}/me/messages";

        try {
            $lastMessageId = null;

            // 1. Send text
            if (trim($text) !== '') {
                $response = Http::withToken($accessToken)
                    ->post($url, [
                        'recipient' => ['id' => $recipientId],
                        'message' => ['text' => $text],
                    ]);

                if ($response->failed()) {
                    Log::error('InstagramDriver: Failed to send text message', [
                        'response' => $response->json(),
                        'conversation_id' => $conversation->id,
                    ]);

                    return [
                        'success' => false,
                        'error' => $response->json('error.message', 'Failed to send message via Instagram.'),
                    ];
                }

                $lastMessageId = $response->json('message_id');
            }

            // 2. Send attachments
            foreach ($attachments as $att) {
                $attUrl = $att['url'] ?? null;
                $attType = $att['type'] ?? 'image';

                if (! $attUrl) {
                    continue;
                }

                $response = Http::withToken($accessToken)
                    ->post($url, [
                        'recipient' => ['id' => $recipientId],
                        'message' => [
                            'attachment' => [
                                'type' => in_array($attType, ['image', 'video', 'audio', 'file']) ? $attType : 'image',
                                'payload' => [
                                    'url' => $attUrl,
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
            Log::error('InstagramDriver: Exception while sending message', [
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
                'fields' => 'name,username,profile_pic',
                'access_token' => $accessToken,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $name = $data['name'] ?? ($data['username'] ?? 'Instagram User');

                return [
                    'name' => $name,
                    'avatar_url' => $data['profile_pic'] ?? null,
                ];
            }
        } catch (\Throwable $e) {
            Log::warning('InstagramDriver: Could not fetch user profile', [
                'sender_id' => $platformSenderId,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }
}
