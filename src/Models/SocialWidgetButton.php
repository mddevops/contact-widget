<?php

namespace SiteApps\ContactWidget\Models;

use SiteApps\ContactWidget\Services\Social\SocialWidgetService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialWidgetButton extends Model
{
    protected $fillable = [
        'widget_id',
        'icon_id',
        'type',
        'title',
        'url',
        'phone',
        'popup_id',
        'popup_title',
        'popup_content',
        'background_color',
        'text_color',
        'sort',
        'enabled',
        'open_type',
    ];

    protected function casts(): array
    {
        return [
            'sort' => 'integer',
            'enabled' => 'boolean',
            'popup_id' => 'integer',
        ];
    }

    public function widget(): BelongsTo
    {
        return $this->belongsTo(SocialWidget::class, 'widget_id');
    }

    public function icon(): BelongsTo
    {
        return $this->belongsTo(SocialIcon::class, 'icon_id');
    }

    public function popup(): BelongsTo
    {
        return $this->belongsTo(Popup::class, 'popup_id');
    }

    public function resolvedHref(): ?string
    {
        return match ($this->open_type) {
            'phone' => filled($this->phone) ? 'tel:' . preg_replace('/\D+/', '', $this->phone) : null,
            'url' => $this->url,
            default => null,
        };
    }

    public function isPopup(): bool
    {
        return $this->open_type === 'popup' && filled($this->popup_id);
    }

    protected static function booted(): void
    {
        static::saved(fn () => SocialWidgetService::forgetCache());
        static::deleted(fn () => SocialWidgetService::forgetCache());
    }
}
