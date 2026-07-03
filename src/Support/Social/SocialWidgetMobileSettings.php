<?php

namespace SiteApps\ContactWidget\Support\Social;

class SocialWidgetMobileSettings
{
    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'position' => 'right',
            'offset_bottom' => 40,
            'offset_side' => 40,
            'tooltip_enabled' => false,
            'show_labels' => true,
            'main_button_size' => 48,
            'main_icon_size' => 30,
            'main_button_color' => '#8e36ff',
            'main_button_text_color' => '#ffffff',
            'item_icon_size' => 18,
            'item_font_size' => 14,
            'panel_background' => '#ffffff',
            'panel_background_opacity' => 100,
            'panel_border_radius' => 6,
        ];
    }

    /**
     * @param  array<string, mixed>|null  $settings
     * @return array<string, mixed>
     */
    public static function merge(?array $settings): array
    {
        return array_replace(self::defaults(), $settings ?? []);
    }

    public static function positionClass(string $position): string
    {
        return match ($position) {
            'right' => 'social-widget--right',
            'center' => 'social-widget--center',
            default => 'social-widget--left',
        };
    }

    public static function displayModeClass(bool $showLabels): string
    {
        return $showLabels ? 'social-widget--labeled' : 'social-widget--icons-only';
    }
}
