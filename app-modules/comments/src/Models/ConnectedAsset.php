<?php

namespace Hotash\Comments\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConnectedAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'platform',
        'asset_id',
        'asset_name',
        'username',
        'avatar_url',
        'parent_asset_id',
        'access_token',
        'is_active',
        'auto_reply_enabled',
        'private_reply_enabled',
        'settings',
        'last_synced_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'auto_reply_enabled' => 'boolean',
        'private_reply_enabled' => 'boolean',
        'settings' => 'array',
        'last_synced_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFacebook(Builder $query): Builder
    {
        return $query->where('platform', 'facebook_page');
    }

    public function scopeInstagram(Builder $query): Builder
    {
        return $query->where('platform', 'instagram_account');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function isFacebook(): bool
    {
        return $this->platform === 'facebook_page';
    }

    public function isInstagram(): bool
    {
        return $this->platform === 'instagram_account';
    }
}
