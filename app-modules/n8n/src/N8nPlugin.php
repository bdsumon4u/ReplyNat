<?php

namespace Hotash\N8n;

use Filament\Contracts\Plugin;
use Filament\Panel;

class N8nPlugin implements Plugin
{
    public function getId(): string
    {
        return 'n8n';
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function register(Panel $panel): void
    {
        $panel
            ->discoverResources(
                in: __DIR__ . '/Filament/Resources',
                for: 'Modules\\N8n\\Filament\\Resources',
            )
            ->discoverPages(
                in: __DIR__ . '/Filament/Pages',
                for: 'Modules\\N8n\\Filament\\Pages',
            )
            ->discoverWidgets(
                in: __DIR__ . '/Filament/Widgets',
                for: 'Modules\\N8n\\Filament\\Widgets',
            );
    }

    public function boot(Panel $panel): void
    {
        //
    }
}