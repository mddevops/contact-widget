<?php

namespace SiteApps\ContactWidget\Database\Seeders;

use SiteApps\ContactWidget\Models\SocialIcon;
use SiteApps\ContactWidget\Models\SocialWidget;
use SiteApps\ContactWidget\Models\SocialWidgetButton;
use SiteApps\ContactWidget\Services\Social\SocialWidgetService;
use Illuminate\Database\Seeder;

class SocialWidgetSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(SocialIconSeeder::class);

        $mainIcon = SocialIcon::query()->where('slug', 'phone')->first();
        $phoneIcon = SocialIcon::query()->where('slug', 'phone')->first();
        $whatsappIcon = SocialIcon::query()->where('slug', 'whatsapp')->first();
        $telegramIcon = SocialIcon::query()->where('slug', 'telegram')->first();

        $widget = SocialWidget::query()->firstOrCreate(
            ['title' => 'Обратная связь'],
            [
                'enabled' => true,
                'show_on_site' => true,
                'main_icon_id' => $mainIcon?->id,
                'main_icon_size' => 30,
                'item_icon_size' => 18,
                'item_font_size' => 14,
                'main_button_size' => 48,
                'main_button_color' => '#8e36ff',
                'main_button_text_color' => '#ffffff',
                'main_button_hover_color' => '#de0611',
                'popup_background' => '#ffffff',
                'panel_background_opacity' => 100,
                'popup_border_radius' => 6,
                'popup_shadow' => '0 5px 10px rgba(0, 0, 0, 0.1)',
                'popup_width' => 280,
                'position' => 'right',
                'offset_bottom' => 40,
                'offset_side' => 40,
                'animation' => 'pulse',
                'tooltip_enabled' => false,
                'show_labels' => true,
                'open_direction' => 'up',
                'mobile_only' => false,
                'desktop_only' => false,
            ],
        );

        $buttons = [
            [
                'icon_id' => $phoneIcon?->id,
                'type' => 'phone',
                'title' => 'Позвонить',
                'phone' => '+78000000000',
                'background_color' => '#22c55e',
                'text_color' => '#ffffff',
                'open_type' => 'phone',
                'sort' => 1,
            ],
            [
                'icon_id' => $whatsappIcon?->id,
                'type' => 'whatsapp',
                'title' => 'WhatsApp',
                'phone' => '+78000000000',
                'background_color' => '#25d366',
                'text_color' => '#ffffff',
                'open_type' => 'whatsapp',
                'sort' => 2,
            ],
            [
                'icon_id' => $telegramIcon?->id,
                'type' => 'telegram',
                'title' => 'Telegram',
                'url' => 'support',
                'background_color' => '#1da1f2',
                'text_color' => '#ffffff',
                'open_type' => 'telegram',
                'sort' => 3,
            ],
            [
                'icon_id' => SocialIcon::query()->where('slug', 'message')->value('id'),
                'type' => 'callback',
                'title' => 'Обратный звонок',
                'popup_title' => 'Оставьте заявку',
                'popup_content' => 'Мы перезвоним вам в ближайшее время.',
                'background_color' => '#8e36ff',
                'text_color' => '#ffffff',
                'open_type' => 'popup',
                'sort' => 4,
            ],
        ];

        foreach ($buttons as $button) {
            SocialWidgetButton::query()->updateOrCreate(
                [
                    'widget_id' => $widget->id,
                    'title' => $button['title'],
                ],
                array_merge($button, [
                    'widget_id' => $widget->id,
                    'enabled' => true,
                ]),
            );
        }

        SocialWidgetService::forgetCache();
    }
}
