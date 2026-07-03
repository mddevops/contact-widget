<?php

namespace SiteApps\ContactWidget\Database\Seeders;

use SiteApps\ContactWidget\Models\SocialIcon;
use SiteApps\ContactWidget\Services\Social\SvgCleaner;
use Illuminate\Database\Seeder;

class SocialIconSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->icons() as $icon) {
            SocialIcon::query()->updateOrCreate(
                ['slug' => $icon['slug']],
                [
                    'title' => $icon['title'],
                    'svg' => SvgCleaner::clean($icon['svg']),
                ],
            );
        }
    }

    /**
     * @return list<array{title: string, slug: string, svg: string}>
     */
    protected function icons(): array
    {
        return [
            [
                'title' => 'Телефон',
                'slug' => 'phone',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1C10.6 21 3 13.4 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1L6.6 10.8z"/></svg>',
            ],
            [
                'title' => 'WhatsApp',
                'slug' => 'whatsapp',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M17.5 14.3c-.3-.2-1.8-.9-2.1-1-.3-.1-.5-.2-.7.2-.2.3-.8 1-.9 1.2-.2.2-.3.2-.6.1-.3-.2-1.2-.4-2.3-1.4-.9-.8-1.5-1.7-1.7-2-.2-.3 0-.5.1-.6.1-.1.3-.3.4-.5.1-.1.1-.3 0-.5-.1-.2-.7-1.7-1-2.3-.3-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.7.3-.2.3-1 1-1 2.5s1 2.9 1.1 3.1c.1.2 2 3.1 4.8 4.3.7.3 1.2.5 1.6.6.7.2 1.3.2 1.8.1.5-.1 1.8-.7 2.1-1.4.2-.7.2-1.2.2-1.4-.1-.1-.3-.2-.6-.3z"/><path d="M12 2C6.5 2 2 6.5 2 12c0 1.8.5 3.5 1.3 5L2 22l5.2-1.4c1.5.8 3.1 1.3 4.8 1.3 5.5 0 10-4.5 10-10S17.5 2 12 2zm0 18.2c-1.6 0-3.1-.4-4.4-1.2l-.3-.2-3.1.8.8-3-.2-.3C4.2 15.1 3.8 13.6 3.8 12c0-4.5 3.7-8.2 8.2-8.2S20.2 7.5 20.2 12 16.5 20.2 12 20.2z"/></svg>',
            ],
            [
                'title' => 'Telegram',
                'slug' => 'telegram',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21.9 4.6 3.5 11.8c-.9.4-.9.9-.2 1.1l4.7 1.5 1.8 5.5c.2.5.4.7.8.7.3 0 .5-.1.8-.4l2.4-2.3 5 3.7c.9.5 1.5.2 1.7-.9L23.7 6.3c.3-1.1-.4-1.6-1.8-1.7zM8.7 13.3l9.9-6.2c.4-.3.8-.1.5.2l-8.2 7.5-.3 3.1-1.1-3.3 8.7-7.5-8.5 6.2z"/></svg>',
            ],
            [
                'title' => 'Max',
                'slug' => 'max',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M4 4h16v16H4V4zm3 3v10h2.2l3.3-5.5L15 17h2.2V7H15l-3.3 5.5L7 7H7z"/></svg>',
            ],
            [
                'title' => 'Автомобиль',
                'slug' => 'car',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M5 11l1.5-4.5C7 5.7 7.8 5 9 5h6c1.2 0 2 .7 2.5 1.5L19 11v8c0 .6-.4 1-1 1h-1c-.6 0-1-.4-1-1v-1H8v1c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-8zm2.2 0h9.6l-1-3H8.2l-1 3zM7 15.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zm10 0a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"/></svg>',
            ],
            [
                'title' => 'Деньги',
                'slug' => 'money',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M3 6h18v12H3V6zm2 2v8h14V8H5zm7 1.5c1.4 0 2.5.6 2.5 1.5S13.4 13 12 13s-2.5.6-2.5 1.5S10.6 16 12 16s2.5-.6 2.5-1.5c0-.2-.1-.4-.2-.5.8-.4 1.2-1 1.2-1.5 0-1.3-1.7-2-3.5-2S8.5 9.7 8.5 11c0 .5.4 1.1 1.2 1.5-.1.1-.2.3-.2.5 0 .9 1.1 1.5 2.5 1.5z"/></svg>',
            ],
            [
                'title' => 'Trade In',
                'slug' => 'trade-in',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M7 7h11v2h1c1.1 0 2 .9 2 2v5c0 1.1-.9 2-2 2h-1v1H6v-1H5c-1.1 0-2-.9-2-2v-5c0-1.1.9-2 2-2h1V7zm11 4H6v5h12v-5zM9 4h6v2H9V4z"/></svg>',
            ],
            [
                'title' => 'Сообщение',
                'slug' => 'message',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M4 4h16a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H8l-4 3v-3H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z"/></svg>',
            ],
            [
                'title' => 'Комментарии',
                'slug' => 'comments',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h3l3 3 3-3h7c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 10H6v-2h12v2zm0-3H6V7h12v2z"/></svg>',
            ],
            [
                'title' => 'Поддержка',
                'slug' => 'support',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2a8 8 0 0 0-8 8v4a3 3 0 0 0 3 3h1v-6H5a6 6 0 1 1 12 0h-3v6h1a3 3 0 0 0 3-3v-4a8 8 0 0 0-8-8zm-1 17h2v2h-2v-2z"/></svg>',
            ],
            [
                'title' => 'Плюс',
                'slug' => 'plus',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M11 4h2v7h7v2h-7v7h-2v-7H4v-2h7V4z"/></svg>',
            ],
            [
                'title' => 'Закрыть',
                'slug' => 'close',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M18.3 5.7 12 12l6.3 6.3-1.4 1.4L10.6 13.4 4.3 19.7 2.9 18.3 9.2 12 2.9 5.7 4.3 4.3l6.3 6.3 6.3-6.3 1.4 1.4z"/></svg>',
            ],
        ];
    }
}
