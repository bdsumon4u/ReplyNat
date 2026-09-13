<?php

use App\Models\User;
use Hotash\Comments\Models\ConnectedAsset;
use Hotash\Comments\Services\MetaGraphService;
use Illuminate\Support\Facades\Http;

test('connected asset model scopes work correctly', function () {
    $user = User::factory()->create();

    $fbPage = ConnectedAsset::create([
        'user_id' => $user->id,
        'platform' => 'facebook_page',
        'asset_id' => 'page_12345',
        'asset_name' => 'Main Facebook Page',
        'access_token' => 'token_fb',
        'is_active' => true,
    ]);

    $igAccount = ConnectedAsset::create([
        'user_id' => $user->id,
        'platform' => 'instagram_account',
        'asset_id' => 'ig_67890',
        'asset_name' => 'main_instagram',
        'username' => 'main_instagram',
        'parent_asset_id' => 'page_12345',
        'access_token' => 'token_fb',
        'is_active' => false,
    ]);

    expect(ConnectedAsset::facebook()->count())->toBe(1)
        ->and(ConnectedAsset::instagram()->count())->toBe(1)
        ->and(ConnectedAsset::active()->count())->toBe(1)
        ->and($fbPage->isFacebook())->toBeTrue()
        ->and($igAccount->isInstagram())->toBeTrue();
});

test('meta graph service generates oauth url correctly', function () {
    config()->set('comments.app_id', '123456789');

    $service = new MetaGraphService;
    $url = $service->getOAuthUrl('https://example.com/callback', 'test_state_123');

    expect($url)
        ->toContain('123456789')
        ->toContain('test_state_123')
        ->toContain('pages_messaging');
});

test('meta graph service exchanges code for long lived token', function () {
    config()->set('comments.app_id', '123456789');
    config()->set('comments.app_secret', 'secret123');

    Http::fake([
        'https://graph.facebook.com/v20.0/oauth/access_token?*client_id=123456789*' => Http::sequence()
            ->push(['access_token' => 'short_lived_token_abc'], 200)
            ->push(['access_token' => 'long_lived_token_xyz'], 200),
    ]);

    $service = new MetaGraphService;
    $token = $service->exchangeCodeForLongLivedToken('auth_code_123', 'https://example.com/callback');

    expect($token)->toBe('long_lived_token_xyz');
});

test('meta graph service fetches user pages and connected instagram accounts', function () {
    Http::fake([
        'https://graph.facebook.com/v20.0/me/accounts*' => Http::response([
            'data' => [
                [
                    'id' => 'page_100',
                    'name' => 'Fashion Store',
                    'access_token' => 'page_token_100',
                    'category' => 'Retail',
                    'picture' => [
                        'data' => [
                            'url' => 'https://example.com/page.png',
                        ],
                    ],
                    'instagram_business_account' => [
                        'id' => 'ig_200',
                        'username' => 'fashionstore_ig',
                        'name' => 'Fashion Store IG',
                        'profile_picture_url' => 'https://example.com/ig.png',
                    ],
                ],
            ],
        ], 200),
    ]);

    $service = new MetaGraphService;
    $pages = $service->fetchUserPagesAndInstagram('user_token_abc');

    expect($pages)->toHaveCount(1)
        ->and($pages[0]['id'])->toBe('page_100')
        ->and($pages[0]['instagram_business_account']['id'])->toBe('ig_200');
});

test('webhook verification succeeds with valid verify token', function () {
    config()->set('comments.webhook_verify_token', 'my_secret_token');

    $response = $this->get('/api/comments/webhook?hub.mode=subscribe&hub.verify_token=my_secret_token&hub.challenge=challenge_token_999');

    $response->assertStatus(200)
        ->assertSee('challenge_token_999');
});

test('webhook verification rejects invalid verify token', function () {
    config()->set('comments.webhook_verify_token', 'my_secret_token');

    $response = $this->get('/api/comments/webhook?hub.mode=subscribe&hub.verify_token=wrong_token&hub.challenge=challenge_token_999');

    $response->assertStatus(403);
});

test('meta oauth callback creates assets for both facebook page and attached instagram account', function () {
    $user = User::factory()->create();

    session(['comments_oauth_state' => 'valid_state']);

    Http::fake([
        'https://graph.facebook.com/v20.0/oauth/access_token*' => Http::sequence()
            ->push(['access_token' => 'short_token'], 200)
            ->push(['access_token' => 'long_token'], 200),
        'https://graph.facebook.com/v20.0/me/accounts*' => Http::response([
            'data' => [
                [
                    'id' => 'page_777',
                    'name' => 'Tech Gadgets',
                    'access_token' => 'page_token_777',
                    'picture' => [
                        'data' => [
                            'url' => 'https://example.com/avatar.jpg',
                        ],
                    ],
                    'instagram_business_account' => [
                        'id' => 'ig_888',
                        'username' => 'techgadgets_ig',
                        'profile_picture_url' => 'https://example.com/ig_avatar.jpg',
                    ],
                ],
            ],
        ], 200),
        'https://graph.facebook.com/v20.0/page_777/subscribed_apps*' => Http::response(['success' => true], 200),
    ]);

    $response = $this->actingAs($user)
        ->get('/comments/auth/callback?code=mock_code&state=valid_state');

    $response->assertRedirect('/app/comment-automation');

    $this->assertDatabaseHas('connected_assets', [
        'user_id' => $user->id,
        'platform' => 'facebook_page',
        'asset_id' => 'page_777',
        'asset_name' => 'Tech Gadgets',
    ]);

    $this->assertDatabaseHas('connected_assets', [
        'user_id' => $user->id,
        'platform' => 'instagram_account',
        'asset_id' => 'ig_888',
        'asset_name' => 'techgadgets_ig',
        'parent_asset_id' => 'page_777',
    ]);
});

test('webhook handles facebook comment event and forwards to n8n webhook', function () {
    $user = User::factory()->create();

    ConnectedAsset::create([
        'user_id' => $user->id,
        'platform' => 'facebook_page',
        'asset_id' => 'page_123',
        'asset_name' => 'Test Page',
        'access_token' => 'token_123',
        'is_active' => true,
    ]);

    config()->set('n8n.url', 'https://n8n.example.com');

    Http::fake([
        "https://n8n.example.com/webhook/comment-trigger-{$user->id}" => Http::response(['success' => true], 200),
    ]);

    $payload = [
        'object' => 'page',
        'entry' => [
            [
                'id' => 'page_123',
                'time' => 1700000000,
                'changes' => [
                    [
                        'field' => 'feed',
                        'value' => [
                            'item' => 'comment',
                            'verb' => 'add',
                            'comment_id' => 'comment_999',
                            'post_id' => 'post_888',
                            'message' => 'How much does this product cost?',
                            'from' => [
                                'id' => 'user_456',
                                'name' => 'Customer John',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    $response = $this->postJson('/api/comments/webhook', $payload);

    $response->assertStatus(200)
        ->assertJson(['status' => 'EVENT_RECEIVED']);

    Http::assertSent(function ($request) use ($user) {
        return $request->url() === "https://n8n.example.com/webhook/comment-trigger-{$user->id}"
            && $request['comment_id'] === 'comment_999'
            && $request['platform'] === 'facebook'
            && $request['comment_text'] === 'How much does this product cost?';
    });
});

test('webhook handles instagram comment event and forwards to n8n webhook', function () {
    $user = User::factory()->create();

    ConnectedAsset::create([
        'user_id' => $user->id,
        'platform' => 'instagram_account',
        'asset_id' => 'ig_555',
        'asset_name' => 'fashion_brand',
        'access_token' => 'token_555',
        'is_active' => true,
    ]);

    config()->set('n8n.url', 'https://n8n.example.com');

    Http::fake([
        "https://n8n.example.com/webhook/comment-trigger-{$user->id}" => Http::response(['success' => true], 200),
    ]);

    $payload = [
        'object' => 'instagram',
        'entry' => [
            [
                'id' => 'ig_555',
                'time' => 1700000000,
                'changes' => [
                    [
                        'field' => 'comments',
                        'value' => [
                            'id' => 'ig_comment_123',
                            'text' => 'Where can I buy this dress?',
                            'from' => [
                                'id' => 'ig_user_789',
                                'username' => 'sara_doe',
                            ],
                            'media' => [
                                'id' => 'media_456',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    $response = $this->postJson('/api/comments/webhook', $payload);

    $response->assertStatus(200)
        ->assertJson(['status' => 'EVENT_RECEIVED']);

    Http::assertSent(function ($request) use ($user) {
        return $request->url() === "https://n8n.example.com/webhook/comment-trigger-{$user->id}"
            && $request['comment_id'] === 'ig_comment_123'
            && $request['platform'] === 'instagram'
            && $request['comment_text'] === 'Where can I buy this dress?';
    });
});

test('webhook relays direct messaging events to chatwoot bot endpoint', function () {
    config()->set('chatwoot.url', 'https://chatwoot.example.com');

    Http::fake([
        'https://chatwoot.example.com/bot' => Http::response(['status' => 'success'], 200),
    ]);

    $payload = [
        'object' => 'page',
        'entry' => [
            [
                'id' => 'page_123',
                'time' => 1700000000,
                'messaging' => [
                    [
                        'sender' => ['id' => 'customer_111'],
                        'recipient' => ['id' => 'page_123'],
                        'message' => [
                            'mid' => 'mid.12345',
                            'text' => 'Hello, I have an inquiry.',
                        ],
                    ],
                ],
            ],
        ],
    ];

    $response = $this->postJson('/api/comments/webhook', $payload, [
        'X-Hub-Signature-256' => 'sha256=mock_signature',
    ]);

    $response->assertStatus(200)
        ->assertJson(['status' => 'EVENT_RECEIVED']);

    Http::assertSent(function ($request) {
        return $request->url() === 'https://chatwoot.example.com/bot'
            && isset($request['entry'][0]['messaging'])
            && $request->hasHeader('X-Hub-Signature-256');
    });
});
