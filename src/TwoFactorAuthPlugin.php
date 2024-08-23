<?php

namespace Vormkracht10\TwoFactorAuth;

use Filament\Contracts\Plugin;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Vormkracht10\TwoFactorAuth\Pages\TwoFactor;

class TwoFactorAuthPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-two-factor-auth';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->login(config('filament-two-factor-auth.login'))
            ->userMenuItems([
                'two-factor-authentication' => MenuItem::make()
                    ->icon('heroicon-o-lock-closed')
                    ->label(__('Two-Factor Authentication'))
                    ->url(fn (): string => TwoFactor::getUrl()),
            ])
            ->pages([
                config('filament-two-factor-auth.two_factor_settings'),
                config('filament-two-factor-auth.challenge'),
            ])
            ->viteTheme('vendor/vormkracht10/filament-two-factor-auth/resources/dist/filament-two-factor-auth.css');
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
