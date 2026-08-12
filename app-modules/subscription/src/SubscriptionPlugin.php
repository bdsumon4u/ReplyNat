<?php

namespace Hotash\Subscription;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Hotash\Subscription\Filament\Pages\Billing;
use Hotash\Subscription\Filament\Pages\ViewInvoice;
use Hotash\Subscription\Filament\Widgets\CurrentPlanWidget;

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
                ViewInvoice::class,
            ])
            ->widgets([
                CurrentPlanWidget::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
