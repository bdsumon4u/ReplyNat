<?php

namespace Hotash\Comments\Http\Controllers;

use Filament\Notifications\Notification;
use Hotash\Comments\Models\ConnectedAsset;
use Hotash\Comments\Services\MetaGraphService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MetaOAuthController extends Controller
{
    /**
     * Redirect to Meta OAuth Authorization Dialog.
     */
    public function redirect(MetaGraphService $meta): RedirectResponse
    {
        $state = Str::random(40);
        session(['comments_oauth_state' => $state]);

        $redirectUri = route('comments.auth.callback');
        $authUrl = $meta->getOAuthUrl($redirectUri, $state);

        return redirect()->away($authUrl);
    }

    /**
     * Handle Meta OAuth Callback.
     */
    public function callback(Request $request, MetaGraphService $meta): RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('filament.app.auth.login');
        }

        $code = $request->get('code');
        $state = $request->get('state');
        $savedState = session()->pull('comments_oauth_state');

        if (! $code || ! $state || $state !== $savedState) {
            Notification::make()
                ->title('Authentication Failed')
                ->body('Invalid or expired state token during Meta authorization.')
                ->danger()
                ->send();

            return redirect('/app/comment-automation');
        }

        $redirectUri = route('comments.auth.callback');
        $longLivedToken = $meta->exchangeCodeForLongLivedToken($code, $redirectUri);

        if (! $longLivedToken) {
            Notification::make()
                ->title('Connection Failed')
                ->body('Could not obtain a long-lived access token from Meta. Please try again.')
                ->danger()
                ->send();

            return redirect('/app/comment-automation');
        }

        // Fetch all Pages and linked Instagram accounts
        $pages = $meta->fetchUserPagesAndInstagram($longLivedToken);
        if (empty($pages)) {
            Notification::make()
                ->title('No Pages Found')
                ->body('No Facebook Pages found for this account. Ensure you are an Admin of at least one Facebook Page.')
                ->warning()
                ->send();

            return redirect('/app/comment-automation');
        }

        $fbCount = 0;
        $igCount = 0;

        foreach ($pages as $page) {
            $pageId = (string) $page['id'];
            $pageName = (string) $page['name'];
            $pageAccessToken = (string) ($page['access_token'] ?? $longLivedToken);
            $avatarUrl = $page['picture']['data']['url'] ?? null;

            // 1. Subscribe Page to App Webhooks
            $meta->subscribePageToWebhook($pageId, $pageAccessToken);

            // 2. Save / Update Facebook Page Asset
            ConnectedAsset::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'platform' => 'facebook_page',
                    'asset_id' => $pageId,
                ],
                [
                    'asset_name' => $pageName,
                    'avatar_url' => $avatarUrl,
                    'access_token' => $pageAccessToken,
                    'is_active' => true,
                    'auto_reply_enabled' => true,
                    'last_synced_at' => now(),
                ]
            );
            $fbCount++;

            // 3. Save / Update Connected Instagram Business Account (if attached)
            $igAccount = $page['instagram_business_account'] ?? null;
            if ($igAccount && ! empty($igAccount['id'])) {
                $igId = (string) $igAccount['id'];
                $igUsername = $igAccount['username'] ?? $igAccount['name'] ?? "ig_{$igId}";
                $igAvatar = $igAccount['profile_picture_url'] ?? null;

                ConnectedAsset::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'platform' => 'instagram_account',
                        'asset_id' => $igId,
                    ],
                    [
                        'asset_name' => $igUsername,
                        'username' => $igUsername,
                        'avatar_url' => $igAvatar,
                        'parent_asset_id' => $pageId,
                        'access_token' => $pageAccessToken,
                        'is_active' => true,
                        'auto_reply_enabled' => true,
                        'last_synced_at' => now(),
                    ]
                );
                $igCount++;
            }
        }

        Notification::make()
            ->title('Meta Assets Connected!')
            ->body("Successfully synced {$fbCount} Facebook Page(s) and {$igCount} Instagram Account(s).")
            ->success()
            ->send();

        return redirect('/app/comment-automation');
    }
}
