<?php

namespace Hotash\Comments;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Hotash\Comments\Filament\Resources\ConnectedAssetResource;

class CommentsPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'comments';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            ConnectedAssetResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
