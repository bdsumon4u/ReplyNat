<?php

namespace Hotash\N8n\Listeners;

use Hotash\N8n\Jobs\SyncN8nWorkflowJob;
use Hotash\Subscription\Models\Subscription;

class SyncN8nWorkflow
{
    /**
     * Handle the event.
     */
    public function handle(Subscription $subscription): void
    {
        if ($subscription->user_id) {
            SyncN8nWorkflowJob::dispatch($subscription->user_id);
        }
    }
}
