<?php



namespace SiteApps\ContactWidget\Models;



use SiteApps\ContactWidget\Enums\PopupListMarkerStyle;

use SiteApps\ContactWidget\Enums\PopupMobileImagePosition;

use SiteApps\ContactWidget\Models\SocialIcon;

use SiteApps\ContactWidget\Services\EmbedService;
use SiteApps\ContactWidget\Services\PopupService;

use SiteApps\ContactWidget\Support\Popup\PopupDisplayRules;
use SiteApps\ContactWidget\Support\Popup\PopupImagePath;

use SiteApps\ContactWidget\Support\Popup\PopupSettings;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Storage;



class Popup extends Model

{

    protected $fillable = [

        'name',

        'title',

        'subtitle',

        'benefits',

        'button_text',

        'image',

        'is_active',

        'sort_order',

        'settings',

        'display_rules',

        'template',

        'ab_test',

        'user_id',

    ];



    protected function casts(): array

    {

        return [

            'is_active' => 'boolean',

            'sort_order' => 'integer',

            'settings' => 'array',

            'display_rules' => 'array',

            'ab_test' => 'array',

        ];

    }



    public function user(): BelongsTo
    {
        return $this->belongsTo(config('contact-widget.user_model'));
    }



    /**

     * @return array<string, mixed>

     */

    public function resolvedSettings(): array

    {

        return PopupSettings::merge($this->settings);

    }



    /**

     * @return array<string, mixed>

     */

    public function resolvedDisplayRules(): array

    {

        return PopupDisplayRules::merge($this->display_rules);

    }



    /**

     * @return list<string>

     */

    public function benefitLines(): array

    {

        return PopupSettings::benefitLines($this->benefits);

    }



    public function listMarkerIcon(): string

    {

        $marker = $this->resolvedSettings()['list_marker'] ?? 'check';



        return PopupListMarkerStyle::tryFrom($marker)?->icon()

            ?? PopupListMarkerStyle::Check->icon();

    }



    public function layoutClass(): string

    {

        return PopupSettings::imagePosition($this->resolvedSettings())->layoutClass();

    }



    public function mobileLayoutClass(): string

    {

        if (! $this->shouldRenderMediaOnMobile()) {

            return 'cbp-layout--mobile-no-image';

        }



        $position = PopupMobileImagePosition::tryFrom($this->resolvedSettings()['mobile_image_position'] ?? 'top')

            ?? PopupMobileImagePosition::Top;



        return $position->layoutClass();

    }



    public function layoutClasses(): string

    {

        $classes = [$this->layoutClass(), $this->mobileLayoutClass()];



        if (! $this->shouldRenderMediaOnDesktop()) {

            $classes[] = 'cbp-layout--desktop-no-image';

        }



        return implode(' ', array_unique($classes));

    }



    public function mediaVisibilityClasses(): string

    {

        $classes = [];



        if (! $this->showImageOnMobile()) {

            $classes[] = 'cbp-media--hide-mobile';

        }



        if (! $this->showImageOnDesktop()) {

            $classes[] = 'cbp-media--hide-desktop';

        }



        return implode(' ', $classes);

    }



    public function hasImage(): bool
    {
        return $this->imageUrl() !== null;
    }

    public function imageUrl(): ?string
    {
        $image = is_array($this->image) ? ($this->image[0] ?? null) : $this->image;

        return PopupImagePath::url($image);
    }



    public function shouldRenderMediaOnDesktop(): bool

    {

        return $this->showImageOnDesktop() && $this->hasImage();

    }



    public function shouldRenderMediaOnMobile(): bool

    {

        return $this->showImageOnMobile() && $this->hasImage();

    }



    public function shouldRenderMedia(): bool

    {

        return $this->shouldRenderMediaOnDesktop() || $this->shouldRenderMediaOnMobile();

    }



    public function showImageOnDesktop(): bool

    {

        return ! PopupSettings::toBool($this->resolvedSettings()['desktop_hide_image'] ?? false);

    }



    public function showImageOnMobile(): bool

    {

        return ! PopupSettings::toBool($this->resolvedSettings()['mobile_hide_image'] ?? false);

    }



    public function buttonIconSvg(): ?string

    {

        $iconId = $this->resolvedSettings()['button_icon_id'] ?? null;



        if (blank($iconId)) {

            return null;

        }



        return SocialIcon::query()->find($iconId)?->svg;

    }



    protected static function booted(): void

    {

        static::creating(function (self $model) {

            if (Auth::check()) {

                $model->user_id = Auth::id();

            }



            if (! $model->sort_order) {

                $maxOrder = static::query()->max('sort_order');



                $model->sort_order = ($maxOrder ?? 0) + 1;

            }



            $model->settings = PopupSettings::merge($model->settings);

            $model->display_rules = PopupDisplayRules::merge($model->display_rules);

        });



        static::saving(function (self $model) {

            $model->settings = PopupSettings::merge($model->settings);

            $model->display_rules = PopupDisplayRules::merge($model->display_rules);

        });



        static::saved(function () {
            PopupService::forgetCache();
            EmbedService::bumpAssetVersion();
        });



        static::deleted(function (self $model) {

            PopupService::forgetCache();
            EmbedService::bumpAssetVersion();



            if ($model->image) {

                Storage::disk('public')->delete($model->image);

            }

        });

    }

}

