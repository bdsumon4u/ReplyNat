<?php

namespace Hotash\Chatwoot;

use Filament\Contracts\Plugin;
use Filament\Panel;

class ChatwootPlugin implements Plugin
{
    public function getId(): string
    {
        return 'chatwoot';
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function register(Panel $panel): void
    {
        $panel
            ->discoverResources(
                in: __DIR__.'/Filament/Resources',
                for: 'Modules\\Chatwoot\\Filament\\Resources',
            )
            ->discoverPages(
                in: __DIR__.'/Filament/Pages',
                for: 'Modules\\Chatwoot\\Filament\\Pages',
            )
            ->discoverWidgets(
                in: __DIR__.'/Filament/Widgets',
                for: 'Modules\\Chatwoot\\Filament\\Widgets',
            );
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
