<?php

namespace Hotash\Inbox\Jobs;

use Hotash\Inbox\Models\Message;
use Hotash\Inbox\Services\ChannelManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendChannelMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Message $message
    ) {}

    public function handle(ChannelManager $channelManager): void
    {
        $conversation = $this->message->conversation;

        if (! $conversation || $this->message->is_private_note) {
            return;
        }

        $result = $channelManager->sendMessage(
            $conversation,
            $this->message->content ?? '',
            $this->message->attachments ?? []
        );

        if ($result['success'] ?? false) {
            $this->message->update([
                'status' => 'sent',
                'platform_message_id' => $result['message_id'] ?? $this->message->platform_message_id,
            ]);
        } else {
            $this->message->update([
                'status' => 'failed',
            ]);
            Log::error('SendChannelMessageJob failed to dispatch message', [
                'message_id' => $this->message->id,
                'error' => $result['error'] ?? 'Unknown error',
            ]);
        }
    }
}
