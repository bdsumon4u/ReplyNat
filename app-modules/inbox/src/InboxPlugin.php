<?php

namespace Hotash\Inbox;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Hotash\Inbox\Filament\Pages\InboxPage;

class InboxPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'inbox';
    }

    public function register(Panel $panel): void
    {
        $panel->pages([
            InboxPage::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
