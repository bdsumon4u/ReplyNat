<?php

namespace Hotash\Chatwoot\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatwootAccount extends Model
{
    protected $fillable = [
        'user_id',
        'chatwoot_account_id',
        'chatwoot_user_id',
        'chatwoot_bot_id',
        'status',
    ];

    protected $casts = [
        'chatwoot_account_id' => 'integer',
        'chatwoot_user_id' => 'integer',
        'chatwoot_bot_id' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
