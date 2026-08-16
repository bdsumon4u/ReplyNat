<?php

namespace Hotash\Subscription;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Hotash\Subscription\Filament\Pages\Billing;
use Hotash\Subscription\Filament\Pages\Credentials;
use Hotash\Subscription\Filament\Pages\ViewInvoice;
use Hotash\Subscription\Filament\Widgets\ChatwootWidget;
use Hotash\Subscription\Filament\Widgets\CurrentPlanWidget;
use Hotash\Subscription\Filament\Widgets\N8nWidget;

class SubscriptionPlugin implements Plugin
{
    public function getId(): string
    {
        return 'subscription';
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function register(Panel $panel): void
    {
        $panel
            ->pages([
                Billing::class,
                Credentials::class,
                ViewInvoice::class,
            ])
            ->widgets([
                CurrentPlanWidget::class,
                ChatwootWidget::class,
                N8nWidget::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
