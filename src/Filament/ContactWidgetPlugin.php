<?php

namespace SiteApps\ContactWidget\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use SiteApps\ContactWidget\Filament\Resources\PopupResource;
use SiteApps\ContactWidget\Filament\Resources\SocialIconResource;
use SiteApps\ContactWidget\Filament\Resources\SocialWidgetResource;

class ContactWidgetPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'contact-widget';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            PopupResource::class,
            SocialWidgetResource::class,
            SocialIconResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
