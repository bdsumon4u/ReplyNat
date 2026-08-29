<?php

namespace Hotash\Chatwoot\Jobs;

use App\Models\User;
use Hotash\Chatwoot\Models\ChatwootAccount;
use Hotash\Chatwoot\Services\ChatwootService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SyncChatwootAccountJob implements ShouldBeUnique, ShouldQueue
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
    public function handle(ChatwootService $chatwoot): void
    {
        $user = User::find($this->userId);
        if (! $user) {
            Log::warning("SyncChatwootAccountJob: User #{$this->userId} not found.");

            return;
        }

        $hasActiveSubscription = $user->activeSubscription() !== null;
        $accountMapping = ChatwootAccount::where('user_id', $user->id)->first();

        if ($hasActiveSubscription) {
            if (! $accountMapping) {
                Log::info("SyncChatwootAccountJob: Provisioning new Chatwoot account for User #{$user->id}");

                // 1. Create Account
                $chatwootAccountId = $chatwoot->createAccount($user->name);
                if (! $chatwootAccountId) {
                    throw new \RuntimeException("Failed to create Chatwoot account for User #{$user->id}");
                }

                // 2. Create User
                $randomPassword = Str::random(12).'!@#$';
                $chatwootUserId = $chatwoot->createUser($user->name, $user->email, $randomPassword);
                if (! $chatwootUserId) {
                    throw new \RuntimeException("Failed to create Chatwoot user for User #{$user->id}");
                }

                // 3. Associate User to Account
                $chatwoot->associateUserToAccount($chatwootAccountId, $chatwootUserId);

                // 4. Create Agent Bot
                $outgoingUrl = rtrim(config('n8n.url', 'http://localhost:5678'), '/').'/webhook/message-trigger-'.$user->id;
                $botData = $chatwoot->createAgentBot('Fari Ha', $chatwootAccountId, $outgoingUrl);
                $chatwootBotId = $botData['id'] ?? null;
                $chatwootBotToken = $botData['access_token'] ?? null;

                if ($chatwootBotToken) {
                    $user->userCredential()->updateOrCreate(
                        ['user_id' => $user->id],
                        ['chatwoot_access_token' => $chatwootBotToken]
                    );
                }

                // 5. Save to Database
                ChatwootAccount::create([
                    'user_id' => $user->id,
                    'chatwoot_account_id' => $chatwootAccountId,
                    'chatwoot_user_id' => $chatwootUserId,
                    'chatwoot_bot_id' => $chatwootBotId,
                    'status' => 'active',
                ]);

                Log::info("SyncChatwootAccountJob: Chatwoot account #{$chatwootAccountId}, user #{$chatwootUserId}, and bot #{$chatwootBotId} successfully provisioned.");
            } elseif ($accountMapping->status === 'suspended') {
                Log::info("SyncChatwootAccountJob: Activating suspended Chatwoot account #{$accountMapping->chatwoot_account_id} for User #{$user->id}");

                $chatwoot->updateAccountStatus($accountMapping->chatwoot_account_id, 'active');

                $accountMapping->update(['status' => 'active']);

                Log::info("SyncChatwootAccountJob: Chatwoot account #{$accountMapping->chatwoot_account_id} activated.");
            }
        } else {
            // Subscription is inactive (expired/canceled)
            if ($accountMapping && $accountMapping->status === 'active') {
                Log::info("SyncChatwootAccountJob: Suspending Chatwoot account #{$accountMapping->chatwoot_account_id} for User #{$user->id}");

                $chatwoot->updateAccountStatus($accountMapping->chatwoot_account_id, 'suspended');

                $accountMapping->update(['status' => 'suspended']);

                Log::info("SyncChatwootAccountJob: Chatwoot account #{$accountMapping->chatwoot_account_id} suspended.");
            }
        }
    }

    /**
     * Handle job failure.
     */
    public function failed(?\Throwable $exception): void
    {
        Log::error("SyncChatwootAccountJob failed for User #{$this->userId}", [
            'error' => $exception?->getMessage(),
        ]);
    }
}
