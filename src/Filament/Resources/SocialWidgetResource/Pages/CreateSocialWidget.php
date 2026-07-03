<?php

namespace SiteApps\ContactWidget\Filament\Resources\SocialWidgetResource\Pages;

use SiteApps\ContactWidget\Filament\Resources\SocialWidgetResource;
use SiteApps\ContactWidget\Filament\Resources\SocialWidgetResource\Concerns\HasSocialWidgetPreview;
use SiteApps\ContactWidget\Filament\Resources\SocialWidgetResource\Concerns\ManagesSocialWidgetOnSiteDisplay;
use SiteApps\ContactWidget\Filament\Resources\SocialWidgetResource\Concerns\ManagesSocialWidgetBuilderLayout;
use SiteApps\ContactWidget\Filament\Resources\SocialWidgetResource\Concerns\ManagesSocialWidgetFormData;
use SiteApps\ContactWidget\Support\Social\SocialWidgetMobileSettings;
use SiteApps\ContactWidget\Models\SocialIcon;
use SiteApps\ContactWidget\Models\SocialWidget;
use Filament\Resources\Pages\CreateRecord;

class CreateSocialWidget extends CreateRecord
{
    use HasSocialWidgetPreview;
    use ManagesSocialWidgetBuilderLayout;
    use ManagesSocialWidgetFormData;
    use ManagesSocialWidgetOnSiteDisplay;

    protected static string $resource = SocialWidgetResource::class;

    protected static string $view = 'contact-widget::filament.resources.social-widget-resource.pages.builder';

    public function mount(): void
    {
        parent::mount();

        $this->form->fill([
            'show_on_site' => ! SocialWidget::query()->where('show_on_site', true)->exists(),
            'position' => 'right',
            'animation' => 'pulse',
            'open_direction' => 'up',
            'main_icon_id' => SocialIcon::query()->where('slug', 'phone')->value('id'),
            'main_button_size' => 48,
            'main_icon_size' => 30,
            'item_icon_size' => 18,
            'item_font_size' => 14,
            'main_button_color' => '#8e36ff',
            'main_button_text_color' => '#ffffff',
            'popup_background' => '#ffffff',
            'panel_background_opacity' => 100,
            'popup_border_radius' => 6,
            'offset_bottom' => 40,
            'offset_side' => 40,
            'tooltip_enabled' => false,
            'show_labels' => true,
            '_button_display' => 'labels',
            '_mobile_button_display' => 'labels',
            'mobile_settings' => SocialWidgetMobileSettings::defaults(),
            'buttons' => [
                [
                    'enabled' => true,
                    'title' => 'Позвонить',
                    'icon_id' => SocialIcon::query()->where('slug', 'phone')->value('id'),
                    'background_color' => '#8e36ff',
                    'text_color' => '#ffffff',
                    'open_type' => 'phone',
                    'sort' => 0,
                ],
            ],
        ]);
    }

    public function getTitle(): string
    {
        return 'Создать виджет';
    }
}
