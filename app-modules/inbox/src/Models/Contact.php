<?php

namespace Hotash\Inbox\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    use HasFactory;

    protected $table = 'inbox_contacts';

    protected $fillable = [
        'user_id',
        'name',
        'avatar_url',
        'email',
        'phone',
        'platform',
        'platform_sender_id',
        'custom_attributes',
    ];

    protected $casts = [
        'custom_attributes' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'contact_id');
    }

    public function getAvatar(): string
    {
        if ($this->avatar_url) {
            return $this->avatar_url;
        }

        $initials = urlencode($this->name ?: 'User');

        return "https://ui-avatars.com/api/?name={$initials}&background=0D8ABC&color=fff";
    }
}
