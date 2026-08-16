<?php

namespace Hotash\N8n\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCredential extends Model
{
    protected $fillable = [
        'user_id',
        'chatwoot_access_token',
        'openai_api_key',
        'facebook_page_id',
        'facebook_page_access_token',
        'instagram_business_account_id',
    ];

    protected $casts = [
        'chatwoot_access_token' => 'encrypted',
        'openai_api_key' => 'encrypted',
        'facebook_page_access_token' => 'encrypted',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
