<?php

use App\Models\User;
use Hotash\Comments\Models\ConnectedAsset;
use Hotash\Inbox\Filament\Pages\InboxPage;
use Hotash\Inbox\Jobs\ProcessMetaWebhookJob;
use Hotash\Inbox\Models\CannedResponse;
use Hotash\Inbox\Models\Contact;
use Hotash\Inbox\Models\Conversation;
use Hotash\Inbox\Models\Message;
use Hotash\Inbox\Services\ChannelManager;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

test('meta webhook challenge succeeds with correct token', function () {
    config()->set('inbox.verify_token', 'test_verify_token_123');

    $response = $this->get('/api/inbox/webhooks/meta?hub_mode=subscribe&hub_verify_token=test_verify_token_123&hub_challenge=challenge_token_xyz');

    $response->assertStatus(200)
        ->assertSee('challenge_token_xyz');
});

test('meta webhook challenge fails with incorrect token', function () {
    config()->set('inbox.verify_token', 'test_verify_token_123');

    $response = $this->get('/api/inbox/webhooks/meta?hub_mode=subscribe&hub_verify_token=wrong_token&hub_challenge=challenge_token_xyz');

    $response->assertStatus(403);
});

test('meta webhook endpoint dispatches background job on post', function () {
    Queue::fake();

    $payload = [
        'object' => 'page',
        'entry' => [
            [
                'id' => 'page_123',
                'time' => 1700000000,
                'messaging' => [
                    [
                        'sender' => ['id' => 'user_psid_456'],
                        'recipient' => ['id' => 'page_123'],
                        'timestamp' => 1700000000,
                        'message' => [
                            'mid' => 'mid_test_12345',
                            'text' => 'Hello from Facebook!',
                        ],
                    ],
                ],
            ],
        ],
    ];

    $response = $this->postJson('/api/inbox/webhooks/meta', $payload);

    $response->assertStatus(200)
        ->assertJson(['status' => 'EVENT_RECEIVED']);

    Queue::assertPushed(ProcessMetaWebhookJob::class);
});

test('process meta webhook job creates contact, conversation, and incoming message for facebook', function () {
    $user = User::factory()->create();

    $asset = ConnectedAsset::create([
        'user_id' => $user->id,
        'platform' => 'facebook_page',
        'asset_id' => 'page_999',
        'asset_name' => 'Demo FB Page',
        'access_token' => 'dummy_token',
        'is_active' => true,
    ]);

    Http::fake([
        'https://graph.facebook.com/*' => Http::response([
            'first_name' => 'Alice',
            'last_name' => 'Smith',
            'profile_pic' => 'https://example.com/alice.jpg',
        ], 200),
    ]);

    $payload = [
        'object' => 'page',
        'entry' => [
            [
                'id' => 'page_999',
                'time' => 1700000000,
                'messaging' => [
                    [
                        'sender' => ['id' => 'psid_alice_123'],
                        'recipient' => ['id' => 'page_999'],
                        'timestamp' => 1700000000,
                        'message' => [
                            'mid' => 'mid_fb_message_001',
                            'text' => 'Hi, how much is this product?',
                        ],
                    ],
                ],
            ],
        ],
    ];

    $job = new ProcessMetaWebhookJob($payload);
    $job->handle(app(ChannelManager::class));

    // Verify Contact
    $contact = Contact::where('platform_sender_id', 'psid_alice_123')->first();
    expect($contact)->not->toBeNull()
        ->and($contact->name)->toBe('Alice Smith')
        ->and($contact->avatar_url)->toBe('https://example.com/alice.jpg');

    // Verify Conversation
    $conversation = Conversation::where('contact_id', $contact->id)->first();
    expect($conversation)->not->toBeNull()
        ->and($conversation->status)->toBe('open')
        ->and($conversation->unread_count)->toBe(1)
        ->and($conversation->last_message_preview)->toBe('Hi, how much is this product?');

    // Verify Message
    $message = Message::where('conversation_id', $conversation->id)->first();
    expect($message)->not->toBeNull()
        ->and($message->content)->toBe('Hi, how much is this product?')
        ->and($message->platform_message_id)->toBe('mid_fb_message_001')
        ->and($message->isIncoming())->toBeTrue();
});

test('process meta webhook job handles instagram direct messages', function () {
    $user = User::factory()->create();

    $asset = ConnectedAsset::create([
        'user_id' => $user->id,
        'platform' => 'instagram_account',
        'asset_id' => 'ig_account_777',
        'asset_name' => 'demo_ig',
        'username' => 'demo_ig',
        'parent_asset_id' => 'page_999',
        'access_token' => 'dummy_token',
        'is_active' => true,
    ]);

    Http::fake([
        'https://graph.facebook.com/*' => Http::response([
            'name' => 'Bob Instagrammer',
            'profile_pic' => 'https://example.com/bob.jpg',
        ], 200),
    ]);

    $payload = [
        'object' => 'instagram',
        'entry' => [
            [
                'id' => 'ig_account_777',
                'time' => 1700000000,
                'messaging' => [
                    [
                        'sender' => ['id' => 'igsid_bob_555'],
                        'recipient' => ['id' => 'ig_account_777'],
                        'timestamp' => 1700000000,
                        'message' => [
                            'mid' => 'mid_ig_msg_002',
                            'text' => 'Hello from IG!',
                        ],
                    ],
                ],
            ],
        ],
    ];

    $job = new ProcessMetaWebhookJob($payload);
    $job->handle(app(ChannelManager::class));

    $contact = Contact::where('platform_sender_id', 'igsid_bob_555')->first();
    expect($contact)->not->toBeNull()
        ->and($contact->name)->toBe('Bob Instagrammer');

    $conversation = Conversation::where('contact_id', $contact->id)->first();
    expect($conversation)->not->toBeNull()
        ->and($conversation->channel)->toBe('instagram');
});

test('channel manager dispatches message to meta graph api', function () {
    $user = User::factory()->create();

    $asset = ConnectedAsset::create([
        'user_id' => $user->id,
        'platform' => 'facebook_page',
        'asset_id' => 'page_999',
        'asset_name' => 'Demo FB Page',
        'access_token' => 'test_access_token',
        'is_active' => true,
    ]);

    $contact = Contact::create([
        'user_id' => $user->id,
        'name' => 'Charlie',
        'platform' => 'facebook',
        'platform_sender_id' => 'psid_charlie_888',
    ]);

    $conversation = Conversation::create([
        'user_id' => $user->id,
        'contact_id' => $contact->id,
        'connected_asset_id' => $asset->id,
        'channel' => 'facebook_page',
        'status' => 'open',
    ]);

    Http::fake([
        'https://graph.facebook.com/v20.0/me/messages' => Http::response([
            'recipient_id' => 'psid_charlie_888',
            'message_id' => 'mid_outbound_sent_999',
        ], 200),
    ]);

    $manager = app(ChannelManager::class);
    $result = $manager->sendMessage($conversation, 'Hello Charlie, thank you for reaching out!');

    expect($result['success'])->toBeTrue()
        ->and($result['message_id'])->toBe('mid_outbound_sent_999');
});

test('filament inbox page component renders and allows sending replies and private notes', function () {
    $user = User::factory()->create();

    $asset = ConnectedAsset::create([
        'user_id' => $user->id,
        'platform' => 'facebook_page',
        'asset_id' => 'page_999',
        'asset_name' => 'Demo FB Page',
        'access_token' => 'test_access_token',
        'is_active' => true,
    ]);

    $contact = Contact::create([
        'user_id' => $user->id,
        'name' => 'Sarah Connor',
        'platform' => 'facebook',
        'platform_sender_id' => 'psid_sarah_111',
    ]);

    $conversation = Conversation::create([
        'user_id' => $user->id,
        'contact_id' => $contact->id,
        'connected_asset_id' => $asset->id,
        'channel' => 'facebook_page',
        'status' => 'open',
        'unread_count' => 2,
    ]);

    CannedResponse::create([
        'user_id' => $user->id,
        'title' => 'Greeting',
        'shortcut' => 'hello',
        'content' => 'Hello! How can we assist you today?',
    ]);

    Http::fake([
        'https://graph.facebook.com/*' => Http::response([
            'recipient_id' => 'psid_sarah_111',
            'message_id' => 'mid_outbound_resp_123',
        ], 200),
    ]);

    $component = Livewire::actingAs($user)
        ->test(InboxPage::class)
        ->assertSuccessful()
        ->assertSee('Sarah Connor')
        ->call('selectConversation', $conversation->id)
        ->set('messageContent', 'Hello! We are here to help.')
        ->call('sendMessage')
        ->assertHasNoErrors();

    // Verify outgoing message was stored
    expect(Message::where('conversation_id', $conversation->id)->count())->toBe(1);

    // Test private team note
    $component->set('isPrivateNote', true)
        ->set('messageContent', 'Customer is asking about pricing tier 2.')
        ->call('sendMessage');

    expect(Message::where('conversation_id', $conversation->id)->where('is_private_note', true)->count())->toBe(1);

    // Test status change
    $component->call('updateConversationStatus', 'closed');
    expect($conversation->fresh()->status)->toBe('closed');
});
