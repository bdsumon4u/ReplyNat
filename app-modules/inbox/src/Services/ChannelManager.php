<?php

namespace Hotash\Inbox\Services;

use Hotash\Inbox\Contracts\ChannelDriverInterface;
use Hotash\Inbox\Drivers\FacebookDriver;
use Hotash\Inbox\Drivers\InstagramDriver;
use Hotash\Inbox\Models\Conversation;
use InvalidArgumentException;

class ChannelManager
{
    /**
     * @var array<string, ChannelDriverInterface>
     */
    protected array $drivers = [];

    public function driver(string $channel): ChannelDriverInterface
    {
        return $this->drivers[$channel] ??= $this->resolve($channel);
    }

    protected function resolve(string $channel): ChannelDriverInterface
    {
        return match ($channel) {
            'facebook_page', 'facebook' => app(FacebookDriver::class),
            'instagram_account', 'instagram' => app(InstagramDriver::class),
            default => throw new InvalidArgumentException("Unsupported channel driver [{$channel}]."),
        };
    }

    /**
     * Send message using the appropriate channel driver.
     *
     * @param  array<int, array{url: string, type: string}>  $attachments
     * @return array{success: bool, message_id?: string, error?: string}
     */
    public function sendMessage(Conversation $conversation, string $text, array $attachments = []): array
    {
        $driver = $this->driver($conversation->channel);

        return $driver->sendMessage($conversation, $text, $attachments);
    }
}
