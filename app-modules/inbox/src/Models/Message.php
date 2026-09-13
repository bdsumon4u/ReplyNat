<?php

namespace Hotash\Inbox\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $table = 'inbox_messages';

    protected $fillable = [
        'conversation_id',
        'sender_type',
        'sender_id',
        'message_type',
        'content',
        'attachments',
        'status',
        'platform_message_id',
        'is_private_note',
        'raw_payload',
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_private_note' => 'boolean',
        'raw_payload' => 'array',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function isIncoming(): bool
    {
        return $this->sender_type === 'contact';
    }

    public function isOutgoing(): bool
    {
        return in_array($this->sender_type, ['agent', 'bot', 'system']);
    }
}
