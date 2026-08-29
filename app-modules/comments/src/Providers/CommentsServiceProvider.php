<?php

namespace Hotash\Comments\Providers;

use Filament\Panel;
use Hotash\Comments\CommentsPlugin;
use Hotash\Comments\Models\ConnectedAsset;
use Hotash\Comments\Policies\ConnectedAssetPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class CommentsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/comments.php', 'comments');

        Panel::configureUsing(function (Panel $panel): void {
            if ($panel->getId() === 'app') {
                $panel->plugin(CommentsPlugin::make());
            }
        });
    }

    public function boot(): void
    {
        Gate::policy(ConnectedAsset::class, ConnectedAssetPolicy::class);

        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../../routes/comments-routes.php');
    }
}
