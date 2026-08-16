<?php

namespace Hotash\N8n\Jobs;

use App\Models\User;
use Hotash\N8n\Models\N8nWorkflow;
use Hotash\N8n\Services\N8nService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncN8nWorkflowJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [10, 60, 300];

    public function __construct(public int $userId) {}

    /**
     * Unique ID for the job to avoid duplicate processing.
     */
    public function uniqueId(): string
    {
        return (string) $this->userId;
    }

    /**
     * Execute the job.
     */
    public function handle(N8nService $n8n): void
    {
        $user = User::find($this->userId);
        if (! $user) {
            Log::warning("SyncN8nWorkflowJob: User #{$this->userId} not found.");

            return;
        }

        $hasActiveSubscription = $user->activeSubscription() !== null;
        $workflowMapping = N8nWorkflow::where('user_id', $user->id)->first();

        if ($hasActiveSubscription) {
            if ($workflowMapping && $workflowMapping->status === 'inactive') {
                Log::info("SyncN8nWorkflowJob: Activating inactive n8n workflow #{$workflowMapping->n8n_workflow_id} for User #{$user->id}");

                $n8n->activateWorkflow($workflowMapping->n8n_workflow_id);

                $workflowMapping->update(['status' => 'active']);

                Log::info("SyncN8nWorkflowJob: n8n workflow #{$workflowMapping->n8n_workflow_id} activated.");
            }
        } else {
            // Subscription is inactive (expired/canceled)
            if ($workflowMapping && $workflowMapping->status === 'active') {
                Log::info("SyncN8nWorkflowJob: Deactivating n8n workflow #{$workflowMapping->n8n_workflow_id} for User #{$user->id}");

                $n8n->deactivateWorkflow($workflowMapping->n8n_workflow_id);

                $workflowMapping->update(['status' => 'inactive']);

                Log::info("SyncN8nWorkflowJob: n8n workflow #{$workflowMapping->n8n_workflow_id} deactivated.");
            }
        }
    }

    /**
     * Handle job failure.
     */
    public function failed(?\Throwable $exception): void
    {
        Log::error("SyncN8nWorkflowJob failed for User #{$this->userId}", [
            'error' => $exception?->getMessage(),
        ]);
    }
}
