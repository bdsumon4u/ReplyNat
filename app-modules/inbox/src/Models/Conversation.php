<?php

namespace Hotash\Inbox\Models;

use App\Models\User;
use Hotash\Comments\Models\ConnectedAsset;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    protected $table = 'inbox_conversations';

    protected $fillable = [
        'user_id',
        'contact_id',
        'connected_asset_id',
        'channel',
        'status',
        'assigned_to',
        'last_message_preview',
        'last_message_at',
        'last_incoming_at',
        'unread_count',
        'metadata',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'last_incoming_at' => 'datetime',
        'unread_count' => 'integer',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function connectedAsset(): BelongsTo
    {
        return $this->belongsTo(ConnectedAsset::class, 'connected_asset_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'conversation_id')->orderBy('created_at', 'asc');
    }

    public function latestMessage(): HasMany
    {
        return $this->hasMany(Message::class, 'conversation_id')->latestOfMany();
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', 'open');
    }

    public function scopeClosed(Builder $query): Builder
    {
        return $query->where('status', 'closed');
    }

    public function scopeSnoozed(Builder $query): Builder
    {
        return $query->where('status', 'snoozed');
    }

    public function isWithinMeta24HourWindow(): bool
    {
        if (! $this->last_incoming_at) {
            return true;
        }

        return $this->last_incoming_at->diffInHours(now()) < 24;
    }

    public function markAsRead(): void
    {
        if ($this->unread_count > 0) {
            $this->update(['unread_count' => 0]);
        }
    }
}
