<?php

namespace Hotash\Inbox\Providers;

use Filament\Panel;
use Hotash\Inbox\InboxPlugin;
use Hotash\Inbox\Services\ChannelManager;
use Illuminate\Support\ServiceProvider;

class InboxServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/inbox.php', 'inbox');

        $this->app->singleton(ChannelManager::class, fn () => new ChannelManager);

        Panel::configureUsing(function (Panel $panel): void {
            if ($panel->getId() === 'app') {
                $panel->plugin(InboxPlugin::make());
            }
        });
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../../routes/inbox-routes.php');
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'inbox');
    }
}
