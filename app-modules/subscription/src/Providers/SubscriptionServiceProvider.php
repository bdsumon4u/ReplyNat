<?php

namespace Hotash\Subscription\Providers;

use Filament\Panel;
use Hotash\Subscription\Console\Commands\ProcessSubscriptionInvoicesAndReminders;
use Hotash\Subscription\Filament\Admin\Resources\InvoiceResource;
use Hotash\Subscription\Filament\Admin\Resources\UserResource;
use Hotash\Subscription\SubscriptionPlugin;
use Illuminate\Support\ServiceProvider;

class SubscriptionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/subscription.php', 'subscription');

        Panel::configureUsing(function (Panel $panel): void {
            if ($panel->getId() === 'admin') {
                $panel->resources([
                    UserResource::class,
                    InvoiceResource::class,
                ]);
            }

            if ($panel->getId() === 'app') {
                $panel->plugin(SubscriptionPlugin::make());
            }
        });
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ProcessSubscriptionInvoicesAndReminders::class,
            ]);
        }
    }
}
