<?php

namespace Hotash\Chatwoot\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatwootService
{
    protected string $baseUrl;

    protected ?string $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('chatwoot.url', 'http://localhost:3000'), '/');
        $this->apiKey = config('chatwoot.api_key');
    }

    /**
     * Get a configured Http pending request instance.
     */
    protected function client(): PendingRequest
    {
        if (empty($this->apiKey)) {
            Log::warning('Chatwoot Platform API Key is not configured.');
        }

        return Http::baseUrl($this->baseUrl)
            ->timeout(8)
            ->connectTimeout(3)
            ->withHeaders([
                'api_access_token' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]);
    }

    /**
     * Create a new Chatwoot Account.
     *
     * @throws RequestException
     */
    public function createAccount(string $name): ?int
    {
        try {
            $response = $this->client()
                ->retry([100, 500, 1000])
                ->post('/platform/api/v1/accounts', [
                    'name' => $name,
                ])
                ->throw();

            return $response->json('id');
        } catch (\Throwable $e) {
            Log::error('Failed to create Chatwoot account', [
                'name' => $name,
                'error' => $this->getErrorMessage($e),
            ]);
            throw $e;
        }
    }

    /**
     * Create a new platform user.
     *
     * @throws RequestException
     */
    public function createUser(string $name, string $email, string $password): ?int
    {
        try {
            $response = $this->client()
                ->retry([100, 500, 1000])
                ->post('/platform/api/v1/users', [
                    'name' => $name,
                    'email' => $email,
                    'password' => $password,
                ])
                ->throw();

            return $response->json('id');
        } catch (\Throwable $e) {
            Log::error('Failed to create Chatwoot user', [
                'name' => $name,
                'email' => $email,
                'error' => $this->getErrorMessage($e),
            ]);
            throw $e;
        }
    }

    /**
     * Associate a user with an account as administrator.
     *
     * @throws RequestException
     */
    public function associateUserToAccount(int $accountId, int $userId): bool
    {
        try {
            $this->client()
                ->retry([100, 500, 1000])
                ->post("/platform/api/v1/accounts/{$accountId}/account_users", [
                    'user_id' => $userId,
                    'role' => 'administrator',
                ])
                ->throw();

            return true;
        } catch (\Throwable $e) {
            Log::error('Failed to associate user to Chatwoot account', [
                'account_id' => $accountId,
                'user_id' => $userId,
                'error' => $this->getErrorMessage($e),
            ]);
            throw $e;
        }
    }

    /**
     * Suspend or unsuspend (activate) an account.
     *
     * @throws RequestException
     */
    public function updateAccountStatus(int $accountId, string $status): bool
    {
        $validStatus = in_array($status, ['active', 'suspended']) ? $status : 'active';

        try {
            $this->client()
                ->retry([100, 500, 1000])
                ->patch("/platform/api/v1/accounts/{$accountId}", [
                    'status' => $validStatus,
                ])
                ->throw();

            return true;
        } catch (\Throwable $e) {
            Log::error('Failed to update Chatwoot account status', [
                'account_id' => $accountId,
                'status' => $validStatus,
                'error' => $this->getErrorMessage($e),
            ]);
            throw $e;
        }
    }

    /**
     * Get SSO login URL for a platform user.
     *
     * @throws RequestException
     */
    public function getLoginUrl(int $chatwootUserId): ?string
    {
        try {
            $response = $this->client()
                ->get("/platform/api/v1/users/{$chatwootUserId}/login")
                ->throw();

            return $response->json('url');
        } catch (\Throwable $e) {
            Log::error('Failed to get Chatwoot SSO login URL', [
                'chatwoot_user_id' => $chatwootUserId,
                'error' => $this->getErrorMessage($e),
            ]);

            return null;
        }
    }

    /**
     * Get access token for a platform user.
     *
     * @throws RequestException
     */
    public function getUserAccessToken(int $chatwootUserId): ?string
    {
        try {
            $response = $this->client()
                ->get("/platform/api/v1/users/{$chatwootUserId}")
                ->throw();

            return $response->json('access_token');
        } catch (\Throwable $e) {
            Log::error('Failed to get Chatwoot user access token', [
                'chatwoot_user_id' => $chatwootUserId,
                'error' => $this->getErrorMessage($e),
            ]);

            return null;
        }
    }

    /**
     * List inboxes for an account using user access token.
     *
     * @throws RequestException
     */
    public function listInboxes(int $accountId, string $userAccessToken): ?array
    {
        try {
            $response = Http::baseUrl($this->baseUrl)
                ->timeout(8)
                ->connectTimeout(3)
                ->withHeaders([
                    'api_access_token' => $userAccessToken,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->get("/api/v1/accounts/{$accountId}/inboxes")
                ->throw();

            return $response->json('payload') ?? $response->json();
        } catch (\Throwable $e) {
            Log::error('Failed to list Chatwoot inboxes', [
                'account_id' => $accountId,
                'error' => $this->getErrorMessage($e),
            ]);

            return null;
        }
    }

    /**
     * Assign an Agent Bot to an Inbox.
     *
     * @throws RequestException
     */
    /**
     * Assign an Agent Bot to an Inbox.
     *
     * @throws RequestException
     */
    public function setAgentBotToInbox(int $accountId, int $inboxId, int|string $agentBotId, string $userAccessToken): bool
    {
        try {
            Http::baseUrl($this->baseUrl)
                ->timeout(15)
                ->connectTimeout(5)
                ->withHeaders([
                    'api_access_token' => $userAccessToken,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->retry([100, 500, 1000])
                ->post("/api/v1/accounts/{$accountId}/inboxes/{$inboxId}/set_agent_bot", [
                    'agent_bot' => $agentBotId,
                ])
                ->throw();

            return true;
        } catch (\Throwable $e) {
            Log::error('Failed to set Agent Bot to Chatwoot inbox', [
                'account_id' => $accountId,
                'inbox_id' => $inboxId,
                'agent_bot' => $agentBotId,
                'error' => $this->getErrorMessage($e),
            ]);

            return false;
        }
    }

    /**
     * Add users/agents as members/collaborators to an inbox.
     *
     * @throws RequestException
     */
    public function addInboxMembers(int $accountId, int $inboxId, array $userIds, string $userAccessToken): bool
    {
        try {
            Http::baseUrl($this->baseUrl)
                ->timeout(15)
                ->connectTimeout(5)
                ->withHeaders([
                    'api_access_token' => $userAccessToken,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->retry([100, 500, 1000])
                ->post("/api/v1/accounts/{$accountId}/inbox_members", [
                    'inbox_id' => $inboxId,
                    'user_ids' => array_values(array_map('intval', $userIds)),
                ])
                ->throw();

            return true;
        } catch (\Throwable $e) {
            Log::error('Failed to add members to Chatwoot inbox', [
                'account_id' => $accountId,
                'inbox_id' => $inboxId,
                'user_ids' => $userIds,
                'error' => $this->getErrorMessage($e),
            ]);

            return false;
        }
    }

    /**
     * Find an inbox by name or webhook URL.
     */
    public function findInboxByName(int $accountId, string $inboxName, string $userAccessToken): ?array
    {
        $inboxes = $this->listInboxes($accountId, $userAccessToken);
        if (! is_array($inboxes)) {
            return null;
        }

        foreach ($inboxes as $inbox) {
            if (($inbox['name'] ?? '') === $inboxName) {
                return $inbox;
            }
        }

        foreach ($inboxes as $inbox) {
            if (($inbox['channel_type'] ?? '') === 'Channel::Api') {
                return $inbox;
            }
        }

        return null;
    }

    /**
     * Find an inbox by name or webhook and assign Agent Bot to it.
     */
    public function assignAgentBotToInboxName(int $accountId, string $inboxName, int|string $agentBotId, string $userAccessToken): ?int
    {
        $matchedInbox = $this->findInboxByName($accountId, $inboxName, $userAccessToken);

        if ($matchedInbox && isset($matchedInbox['id'])) {
            $inboxId = (int) $matchedInbox['id'];
            $this->setAgentBotToInbox($accountId, $inboxId, $agentBotId, $userAccessToken);

            return $inboxId;
        }

        return null;
    }

    /**
     * Create an Agent Bot on the platform.
     *
     * @throws RequestException
     */
    public function createAgentBot(string $name, int $accountId, string $outgoingUrl): ?array
    {
        try {
            $response = $this->client()
                ->retry([100, 500, 1000])
                ->post('/platform/api/v1/agent_bots', [
                    'name' => $name,
                    'account_id' => $accountId,
                    'outgoing_url' => $outgoingUrl,
                ])
                ->throw();

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('Failed to create Chatwoot Agent Bot', [
                'name' => $name,
                'account_id' => $accountId,
                'outgoing_url' => $outgoingUrl,
                'error' => $this->getErrorMessage($e),
            ]);

            return null;
        }
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
