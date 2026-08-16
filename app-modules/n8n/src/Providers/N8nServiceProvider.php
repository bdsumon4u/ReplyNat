<?php

namespace Hotash\N8n\Providers;

use Hotash\N8n\Listeners\SyncN8nWorkflow;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class N8nServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/n8n.php', 'n8n');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        Event::listen(
            'eloquent.saved: Hotash\Subscription\Models\Subscription',
            SyncN8nWorkflow::class
        );

        Event::listen(
            'eloquent.deleted: Hotash\Subscription\Models\Subscription',
            SyncN8nWorkflow::class
        );
    }
}
