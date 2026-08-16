<?php

namespace Hotash\Chatwoot\Providers;

use Hotash\Chatwoot\Listeners\SyncChatwootAccount;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class ChatwootServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/chatwoot.php', 'chatwoot');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../../routes/chatwoot-routes.php');

        Event::listen(
            'eloquent.saved: Hotash\Subscription\Models\Subscription',
            SyncChatwootAccount::class
        );

        Event::listen(
            'eloquent.deleted: Hotash\Subscription\Models\Subscription',
            SyncChatwootAccount::class
        );
    }
}
