<?php

namespace SiteApps\ContactWidget\Services\Social;

use SiteApps\ContactWidget\Models\SocialIcon;
use SiteApps\ContactWidget\Models\SocialWidget;
use Illuminate\Support\Facades\Cache;

class SocialWidgetService
{
    public const CACHE_KEY = 'social_widget.active';

    public static function active(): ?SocialWidget
    {
        return Cache::remember(self::CACHE_KEY, now()->addHour(), function () {
            return SocialWidget::query()
                ->with([
                    'mainIcon',
                    'buttons' => fn ($query) => $query->orderBy('sort'),
                    'buttons.icon',
                    'buttons.popup',
                ])
                ->where('show_on_site', true)
                ->orderBy('id')
                ->first();
        });
    }

    public static function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * @return array<int, string>
     */
    public static function iconOptions(): array
    {
        return SocialIcon::query()
            ->orderBy('title')
            ->pluck('title', 'id')
            ->all();
    }
}
