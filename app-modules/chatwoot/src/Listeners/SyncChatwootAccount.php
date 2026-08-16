<?php

namespace Hotash\Chatwoot\Listeners;

use Hotash\Chatwoot\Jobs\SyncChatwootAccountJob;
use Hotash\Subscription\Models\Subscription;

class SyncChatwootAccount
{
    /**
     * Handle the event.
     */
    public function handle(Subscription $subscription): void
    {
        if ($subscription->user_id) {
            SyncChatwootAccountJob::dispatch($subscription->user_id);
        }
    }
}
