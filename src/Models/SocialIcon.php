<?php

namespace SiteApps\ContactWidget\Models;

use SiteApps\ContactWidget\Services\Social\SvgCleaner;
use SiteApps\ContactWidget\Services\Social\SocialWidgetService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SocialIcon extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'svg',
    ];

    public function widgets(): HasMany
    {
        return $this->hasMany(SocialWidget::class, 'main_icon_id');
    }

    public function buttons(): HasMany
    {
        return $this->hasMany(SocialWidgetButton::class, 'icon_id');
    }

    public function selectOptionHtml(): string
    {
        $title = e($this->title);
        $svg = $this->svg ?? '';

        return '<span class="flex items-center gap-2">'
            .'<span class="inline-flex h-5 w-5 shrink-0 items-center justify-center text-gray-700 dark:text-gray-200 [&_svg]:h-full [&_svg]:w-full">'.$svg.'</span>'
            .'<span class="truncate">'.$title.'</span>'
            .'</span>';
    }

    protected static function booted(): void
    {
        static::saving(function (self $icon): void {
            if (filled($icon->title) && blank($icon->slug)) {
                $icon->slug = Str::slug($icon->title);
            }

            if (filled($icon->svg)) {
                $icon->svg = SvgCleaner::clean($icon->svg);
            }
        });

        static::saved(fn () => SocialWidgetService::forgetCache());
        static::deleted(fn () => SocialWidgetService::forgetCache());
    }
}
