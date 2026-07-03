<?php

namespace SiteApps\ContactWidget\Models;

use SiteApps\ContactWidget\Support\Social\SocialWidgetMobileSettings;
use SiteApps\ContactWidget\Services\EmbedService;
use SiteApps\ContactWidget\Services\Social\SocialWidgetService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SocialWidget extends Model
{
    protected $fillable = [
        'enabled',
        'show_on_site',
        'title',
        'main_icon_id',
        'main_icon_size',
        'item_icon_size',
        'item_font_size',
        'main_button_size',
        'main_button_color',
        'main_button_text_color',
        'main_button_hover_color',
        'popup_background',
        'panel_background_opacity',
        'popup_border_radius',
        'popup_shadow',
        'popup_width',
        'position',
        'offset_bottom',
        'offset_side',
        'animation',
        'tooltip_enabled',
        'show_labels',
        'open_direction',
        'mobile_only',
        'desktop_only',
        'mobile_settings',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'show_on_site' => 'boolean',
            'main_icon_size' => 'integer',
            'item_icon_size' => 'integer',
            'item_font_size' => 'integer',
            'main_button_size' => 'integer',
            'popup_border_radius' => 'integer',
            'panel_background_opacity' => 'integer',
            'popup_width' => 'integer',
            'offset_bottom' => 'integer',
            'offset_side' => 'integer',
            'tooltip_enabled' => 'boolean',
            'show_labels' => 'boolean',
            'mobile_only' => 'boolean',
            'desktop_only' => 'boolean',
            'mobile_settings' => 'array',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function mergedMobileSettings(): array
    {
        return SocialWidgetMobileSettings::merge($this->mobile_settings);
    }

    public function positionClass(?string $position = null): string
    {
        $position ??= $this->position ?? 'left';

        return SocialWidgetMobileSettings::positionClass($position);
    }

    public function mainIcon(): BelongsTo
    {
        return $this->belongsTo(SocialIcon::class, 'main_icon_id');
    }

    public function buttons(): HasMany
    {
        return $this->hasMany(SocialWidgetButton::class, 'widget_id')->orderBy('sort');
    }

    public function enabledButtons(): HasMany
    {
        return $this->buttons()->where('enabled', true);
    }

    public function animationClass(): string
    {
        $animation = $this->animation ?? 'none';

        return $animation === 'none' ? '' : 'social-widget__main--'.$animation;
    }

    public function displayModeClass(bool $mobile = false): string
    {
        if ($mobile) {
            $settings = $this->mergedMobileSettings();

            return SocialWidgetMobileSettings::displayModeClass((bool) ($settings['show_labels'] ?? false));
        }

        return SocialWidgetMobileSettings::displayModeClass($this->show_labels && ! $this->tooltip_enabled);
    }

    public static function currentlyOnSite(?int $exceptId = null): ?self
    {
        return self::query()
            ->where('show_on_site', true)
            ->when($exceptId, fn ($query) => $query->where('id', '!=', $exceptId))
            ->orderBy('id')
            ->first();
    }

    public static function clearOnSiteExcept(?int $exceptId = null): void
    {
        self::query()
            ->where('show_on_site', true)
            ->when($exceptId, fn ($query) => $query->where('id', '!=', $exceptId))
            ->update(['show_on_site' => false]);
    }

    protected static function booted(): void
    {
        static::saved(function () {
            SocialWidgetService::forgetCache();
            EmbedService::bumpAssetVersion();
        });
        static::deleted(function () {
            SocialWidgetService::forgetCache();
            EmbedService::bumpAssetVersion();
        });
    }
}
