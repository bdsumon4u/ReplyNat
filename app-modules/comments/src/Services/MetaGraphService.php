<?php

namespace Hotash\Comments\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaGraphService
{
    protected string $appId;

    protected string $appSecret;

    protected string $graphVersion;

    protected int $timeout;

    protected int $connectTimeout;

    public function __construct()
    {
        $this->appId = (string) config('comments.app_id');
        $this->appSecret = (string) config('comments.app_secret');
        $this->graphVersion = (string) config('comments.graph_version', 'v20.0');
        $this->timeout = (int) config('comments.timeout', 15);
        $this->connectTimeout = (int) config('comments.connect_timeout', 5);
    }

    /**
     * Get a configured HTTP client for Meta Graph API.
     */
    protected function client(): PendingRequest
    {
        return Http::baseUrl("https://graph.facebook.com/{$this->graphVersion}")
            ->timeout($this->timeout)
            ->connectTimeout($this->connectTimeout)
            ->withHeaders([
                'Accept' => 'application/json',
            ]);
    }

    /**
     * Generate 1-Click OAuth URL for Facebook & Instagram Asset authorization.
     */
    public function getOAuthUrl(string $redirectUri, string $state): string
    {
        $scopes = config('comments.scopes', [
            'pages_show_list',
            'pages_read_engagement',
            'pages_manage_posts',
            'pages_read_user_content',
            'pages_messaging',
            'instagram_basic',
            'instagram_manage_comments',
            'instagram_manage_messages',
        ]);

        $query = http_build_query([
            'client_id' => $this->appId,
            'redirect_uri' => $redirectUri,
            'state' => $state,
            'scope' => implode(',', $scopes),
            'response_type' => 'code',
            'auth_type' => 'rerequest',
        ]);

        return "https://www.facebook.com/{$this->graphVersion}/dialog/oauth?{$query}";
    }

    /**
     * Exchange OAuth Code for a Long-Lived User Access Token.
     */
    public function exchangeCodeForLongLivedToken(string $code, string $redirectUri): ?string
    {
        try {
            // 1. Get Short-Lived User Token
            $shortResponse = $this->client()->get('/oauth/access_token', [
                'client_id' => $this->appId,
                'client_secret' => $this->appSecret,
                'redirect_uri' => $redirectUri,
                'code' => $code,
            ])->throw();

            $shortLivedToken = $shortResponse->json('access_token');
            if (! $shortLivedToken) {
                return null;
            }

            // 2. Exchange for 60-day Long-Lived User Token
            $longResponse = $this->client()->get('/oauth/access_token', [
                'grant_type' => 'fb_exchange_token',
                'client_id' => $this->appId,
                'client_secret' => $this->appSecret,
                'fb_exchange_token' => $shortLivedToken,
            ])->throw();

            return $longResponse->json('access_token') ?? $shortLivedToken;
        } catch (\Throwable $e) {
            Log::error('MetaGraphService: Failed to exchange OAuth code for long-lived token', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Fetch all managed Facebook Pages and connected Instagram Business accounts.
     *
     * @return array<int, array>
     */
    public function fetchUserPagesAndInstagram(string $userAccessToken): array
    {
        try {
            $response = $this->client()->get('/me/accounts', [
                'fields' => 'id,name,access_token,category,picture{url},instagram_business_account{id,username,name,profile_picture_url}',
                'limit' => 100,
                'access_token' => $userAccessToken,
            ])->throw();

            return $response->json('data') ?? [];
        } catch (\Throwable $e) {
            Log::error('MetaGraphService: Failed to fetch user pages and instagram accounts', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Subscribe a Facebook Page to the Meta App's webhooks.
     */
    public function subscribePageToWebhook(string $pageId, string $pageAccessToken): bool
    {
        try {
            $response = $this->client()->post("/{$pageId}/subscribed_apps", [
                'subscribed_fields' => 'feed,conversations,messages',
                'access_token' => $pageAccessToken,
            ])->throw();

            return (bool) ($response->json('success') ?? true);
        } catch (\Throwable $e) {
            Log::error("MetaGraphService: Failed to subscribe Page #{$pageId} to webhooks", [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Reply publicly to a Facebook post comment.
     */
    public function replyToFacebookComment(string $commentId, string $message, string $pageAccessToken): ?array
    {
        try {
            $response = $this->client()->post("/{$commentId}/comments", [
                'message' => $message,
                'access_token' => $pageAccessToken,
            ])->throw();

            return $response->json();
        } catch (\Throwable $e) {
            Log::error("MetaGraphService: Failed to reply to Facebook comment #{$commentId}", [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Reply publicly to an Instagram comment.
     */
    public function replyToInstagramComment(string $igCommentId, string $message, string $pageAccessToken): ?array
    {
        try {
            $response = $this->client()->post("/{$igCommentId}/replies", [
                'message' => $message,
                'access_token' => $pageAccessToken,
            ])->throw();

            return $response->json();
        } catch (\Throwable $e) {
            Log::error("MetaGraphService: Failed to reply to Instagram comment #{$igCommentId}", [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Send a private DM reply to a commenter on Facebook or Instagram.
     */
    public function sendPrivateReply(string $assetId, string $commentId, string $message, string $pageAccessToken): ?array
    {
        try {
            $response = $this->client()->post("/{$assetId}/messages", [
                'recipient' => [
                    'comment_id' => $commentId,
                ],
                'message' => [
                    'text' => $message,
                ],
                'access_token' => $pageAccessToken,
            ])->throw();

            return $response->json();
        } catch (\Throwable $e) {
            Log::error("MetaGraphService: Failed to send private DM reply for comment #{$commentId}", [
                'asset_id' => $assetId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Verify Meta Webhook SHA256 Signature.
     */
    public function verifySignature(string $payload, ?string $signatureHeader): bool
    {
        if (empty($signatureHeader) || empty($this->appSecret)) {
            return false;
        }

        $expectedHash = hash_hmac('sha256', $payload, $this->appSecret);
        $signature = str_replace('sha256=', '', $signatureHeader);

        return hash_equals($expectedHash, $signature);
    }
}
