<?php

namespace Hotash\N8n\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class N8nWorkflow extends Model
{
    protected $fillable = [
        'user_id',
        'n8n_workflow_id',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
