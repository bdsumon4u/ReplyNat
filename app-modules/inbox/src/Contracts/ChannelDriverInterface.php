<?php

namespace Hotash\Inbox\Contracts;

use Hotash\Inbox\Models\Conversation;

interface ChannelDriverInterface
{
    /**
     * Send an outgoing message to the platform (Facebook Page, Instagram DM, etc.).
     *
     * @param  array<int, array{url: string, type: string}>  $attachments
     * @return array{success: bool, message_id?: string, error?: string}
     */
    public function sendMessage(Conversation $conversation, string $text, array $attachments = []): array;

    /**
     * Fetch the contact profile (name, avatar, etc.) from the platform.
     *
     * @return array{name?: string, avatar_url?: string}|null
     */
    public function fetchContactProfile(string $platformSenderId, string $accessToken): ?array;
}
