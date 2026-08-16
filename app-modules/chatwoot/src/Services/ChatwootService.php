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
            ->timeout(15)
            ->connectTimeout(5)
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
                ->retry([100, 500, 1000])
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
