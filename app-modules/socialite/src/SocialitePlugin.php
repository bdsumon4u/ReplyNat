<?php

namespace Hotash\Socialite;

use Filament\Contracts\Plugin;
use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;

class SocialitePlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'socialite';
    }

    public function register(Panel $panel): void
    {
        FilamentView::registerRenderHook(PanelsRenderHook::AUTH_LOGIN_FORM_AFTER, function () {
            return Filament::getCurrentPanel()?->hasPlugin($this->getId()) ? view('socialite::socialite-login') : '';
        });
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
