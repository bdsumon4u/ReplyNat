<?php

namespace Hotash\N8n\Services;

use App\Models\User;
use Hotash\N8n\Models\N8nWorkflow;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class N8nService
{
    protected string $baseUrl;

    protected ?string $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('n8n.url', 'http://localhost:5678'), '/');
        $this->apiKey = config('n8n.api_key');
    }

    /**
     * Get a configured Http pending request instance.
     */
    protected function client(): PendingRequest
    {
        if (empty($this->apiKey)) {
            Log::warning('n8n REST API Key is not configured.');
        }

        return Http::baseUrl($this->baseUrl)
            ->timeout(15)
            ->connectTimeout(5)
            ->withHeaders([
                'X-N8N-API-KEY' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]);
    }

    /**
     * Create a new n8n workflow.
     *
     * @throws RequestException
     */
    public function createWorkflow(string $name, array|object $nodes = [], array|object $connections = [], array|object $settings = []): ?string
    {
        try {
            $response = $this->client()
                ->retry([100, 500, 1000])
                ->post('/api/v1/workflows', [
                    'name' => $name,
                    'nodes' => $nodes,
                    'connections' => $connections,
                    'settings' => $settings,
                ])
                ->throw();

            return $response->json('id');
        } catch (\Throwable $e) {
            Log::error('Failed to create n8n workflow', [
                'name' => $name,
                'error' => $this->getErrorMessage($e),
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing n8n workflow.
     *
     * @throws RequestException
     */
    public function updateWorkflow(string $workflowId, string $name, array|object $nodes = [], array|object $connections = [], array|object $settings = []): bool
    {
        try {
            $this->client()
                ->retry([100, 500, 1000])
                ->put("/api/v1/workflows/{$workflowId}", [
                    'name' => $name,
                    'nodes' => $nodes,
                    'connections' => $connections,
                    'settings' => $settings,
                ])
                ->throw();

            return true;
        } catch (\Throwable $e) {
            Log::error('Failed to update n8n workflow', [
                'workflow_id' => $workflowId,
                'error' => $this->getErrorMessage($e),
            ]);
            throw $e;
        }
    }

    /**
     * Activate a workflow.
     *
     * @throws RequestException
     */
    public function activateWorkflow(string $workflowId): bool
    {
        try {
            $this->client()
                ->retry([100, 500, 1000])
                ->post("/api/v1/workflows/{$workflowId}/activate", (object) [])
                ->throw();

            return true;
        } catch (\Throwable $e) {
            Log::error('Failed to activate n8n workflow', [
                'workflow_id' => $workflowId,
                'error' => $this->getErrorMessage($e),
            ]);
            throw $e;
        }
    }

    /**
     * Deactivate a workflow.
     *
     * @throws RequestException
     */
    public function deactivateWorkflow(string $workflowId): bool
    {
        try {
            $this->client()
                ->retry([100, 500, 1000])
                ->post("/api/v1/workflows/{$workflowId}/deactivate", (object) [])
                ->throw();

            return true;
        } catch (\Throwable $e) {
            Log::error('Failed to deactivate n8n workflow', [
                'workflow_id' => $workflowId,
                'error' => $this->getErrorMessage($e),
            ]);
            throw $e;
        }
    }

    /**
     * Import and activate a user's workflow using their credentials.
     *
     * @throws \Exception
     */
    public function importUserWorkflow(User $user): string
    {
        $chatwootAccount = $user->chatwootAccount;
        if (! $chatwootAccount) {
            throw new \RuntimeException('Chatwoot account must be provisioned before setting up n8n workflow.');
        }

        $credentials = $user->userCredential;
        if (! $credentials) {
            throw new \RuntimeException('User credentials must be configured before setting up n8n workflow.');
        }

        // 1. Read template
        $templatePath = __DIR__.'/../../resources/workflow-template.json';
        if (! file_exists($templatePath)) {
            throw new \RuntimeException("n8n workflow template file not found at: {$templatePath}");
        }

        $templateJson = file_get_contents($templatePath);

        // 2. Perform replacements
        $replacements = [
            '{{CHATWOOT_URL}}' => rtrim(config('chatwoot.url', 'http://localhost:3000'), '/'),
            '{{CHATWOOT_ACCOUNT_ID}}' => $chatwootAccount->chatwoot_account_id,
            '{{CHATWOOT_ACCESS_TOKEN}}' => $credentials->chatwoot_access_token ?? '',
            '{{OPENAI_API_KEY}}' => $credentials->openai_api_key ?? '',
            '{{FACEBOOK_PAGE_ID}}' => $credentials->facebook_page_id ?? '',
            '{{FACEBOOK_PAGE_ACCESS_TOKEN}}' => $credentials->facebook_page_access_token ?? '',
            '{{INSTAGRAM_BUSINESS_ACCOUNT_ID}}' => $credentials->instagram_business_account_id ?? '',
            '{{WEBHOOK_TRIGGER_PATH}}' => 'webhook-trigger-'.$user->id,
        ];

        $substitutedJson = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $templateJson
        );

        $template = json_decode($substitutedJson, false);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Failed to parse n8n workflow template JSON: '.json_last_error_msg());
        }

        $workflowName = "{$user->name}'s Workflow";
        $nodes = $template->nodes ?? [];
        $connections = $template->connections ?? (object) [];
        $settings = $template->settings ?? (object) [];

        // 3. Create or Update Workflow in n8n
        $workflowMapping = N8nWorkflow::where('user_id', $user->id)->first();

        if ($workflowMapping && $workflowMapping->n8n_workflow_id) {
            $n8nWorkflowId = $workflowMapping->n8n_workflow_id;
            $this->updateWorkflow($n8nWorkflowId, $workflowName, $nodes, $connections, $settings);
        } else {
            $n8nWorkflowId = $this->createWorkflow($workflowName, $nodes, $connections, $settings);
            if (! $n8nWorkflowId) {
                throw new \RuntimeException("Failed to create n8n workflow for User #{$user->id}");
            }
        }

        // 4. Activate it
        $this->activateWorkflow($n8nWorkflowId);

        return $n8nWorkflowId;
    }

    /**
     * Get a detailed error message from a throwable.
     */
    protected function getErrorMessage(\Throwable $e): string
    {
        if ($e instanceof RequestException) {
            $body = $e->response->body();

            return ! empty($body) ? $body : $e->getMessage();
        }

        return $e->getMessage();
    }
}
