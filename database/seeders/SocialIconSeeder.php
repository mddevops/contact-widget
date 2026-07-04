<?php

namespace SiteApps\ContactWidget\Database\Seeders;

use SiteApps\ContactWidget\Models\SocialIcon;
use SiteApps\ContactWidget\Services\Social\SvgCleaner;
use Illuminate\Database\Seeder;

class SocialIconSeeder extends Seeder
{
    public function run(): void
    {
        $slugs = [];

        foreach ($this->icons() as $icon) {
            $slugs[] = $icon['slug'];

            SocialIcon::query()->updateOrCreate(
                ['slug' => $icon['slug']],
                [
                    'title' => $icon['title'],
                    'svg' => SvgCleaner::clean($icon['svg']),
                ],
            );
        }

        SocialIcon::query()->whereNotIn('slug', $slugs)->delete();
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
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 52 52" enable-background="new 0 0 52 52" xml:space="preserve"><g id="SVGRepo_bgCarrier" stroke-width="0"/><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/><g id="SVGRepo_iconCarrier"> <path d="M48.5,37.9L42.4,33c-1.4-1.1-3.4-1.2-4.8-0.1l-5.2,3.8c-0.6,0.5-1.5,0.4-2.1-0.2l-7.8-7l-7-7.8 c-0.6-0.6-0.6-1.4-0.2-2.1l3.8-5.2c1.1-1.4,1-3.4-0.1-4.8l-4.9-6.1c-1.5-1.8-4.2-2-5.9-0.3L3,8.4c-0.8,0.8-1.2,1.9-1.2,3 c0.5,10.2,5.1,19.9,11.9,26.7S30.2,49.5,40.4,50c1.1,0.1,2.2-0.4,3-1.2l5.2-5.2C50.5,42.1,50.4,39.3,48.5,37.9z" fill="currentColor"/> </g></svg>',
            ],
            [
                'title' => 'WhatsApp',
                'slug' => 'whatsapp',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"><g id="SVGRepo_bgCarrier" stroke-width="0"/><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/><g id="SVGRepo_iconCarrier"> <g>  <path fill-rule="nonzero" d="M2.004 22l1.352-4.968A9.954 9.954 0 0 1 2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10a9.954 9.954 0 0 1-5.03-1.355L2.004 22zM8.391 7.308a.961.961 0 0 0-.371.1 1.293 1.293 0 0 0-.294.228c-.12.113-.188.211-.261.306A2.729 2.729 0 0 0 6.9 9.62c.002.49.13.967.33 1.413.409.902 1.082 1.857 1.971 2.742.214.213.423.427.648.626a9.448 9.448 0 0 0 3.84 2.046l.569.087c.185.01.37-.004.556-.013a1.99 1.99 0 0 0 .833-.231c.166-.088.244-.132.383-.22 0 0 .043-.028.125-.09.135-.1.218-.171.33-.288.083-.086.155-.187.21-.302.078-.163.156-.474.188-.733.024-.198.017-.306.014-.373-.004-.107-.093-.218-.19-.265l-.582-.261s-.87-.379-1.401-.621a.498.498 0 0 0-.177-.041.482.482 0 0 0-.378.127v-.002c-.005 0-.072.057-.795.933a.35.35 0 0 1-.368.13 1.416 1.416 0 0 1-.191-.066c-.124-.052-.167-.072-.252-.109l-.005-.002a6.01 6.01 0 0 1-1.57-1c-.126-.11-.243-.23-.363-.346a6.296 6.296 0 0 1-1.02-1.268l-.059-.095a.923.923 0 0 1-.102-.205c-.038-.147.061-.265.061-.265s.243-.266.356-.41a4.38 4.38 0 0 0 .263-.373c.118-.19.155-.385.093-.536-.28-.684-.57-1.365-.868-2.041-.059-.134-.234-.23-.393-.249-.054-.006-.108-.012-.162-.016a3.385 3.385 0 0 0-.403.004z" fill="currentColor"/> </g> </g></svg>',
            ],
            [
                'title' => 'Telegram',
                'slug' => 'telegram',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><g id="SVGRepo_bgCarrier" stroke-width="0"/><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/><g id="SVGRepo_iconCarrier"><path d="m20.665 3.717-17.73 6.837c-1.21.486-1.203 1.161-.222 1.462l4.552 1.42 10.532-6.645c.498-.303.953-.14.579.192l-8.533 7.701h-.002l.002.001-.314 4.692c.46 0 .663-.211.921-.46l2.211-2.15 4.599 3.397c.848.467 1.457.227 1.668-.785l3.019-14.228c.309-1.239-.473-1.8-1.282-1.434z" fill="currentColor"/></g></svg>',
            ],
            [
                'title' => 'Max',
                'slug' => 'max',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 720 720" fill="none"><path d="M350.4,9.6C141.8,20.5,4.1,184.1,12.8,390.4c3.8,90.3,40.1,168,48.7,253.7,2.2,22.2-4.2,49.6,21.4,59.3,31.5,11.9,79.8-8.1,106.2-26.4,9-6.1,17.6-13.2,24.2-22,27.3,18.1,53.2,35.6,85.7,43.4,143.1,34.3,299.9-44.2,369.6-170.3C799.6,291.2,622.5-4.6,350.4,9.6h0ZM269.4,504c-11.3,8.8-22.2,20.8-34.7,27.7-18.1,9.7-23.7-.4-30.5-16.4-21.4-50.9-24-137.6-11.5-190.9,16.8-72.5,72.9-136.3,150-143.1,78-6.9,150.4,32.7,183.1,104.2,72.4,159.1-112.9,316.2-256.4,218.6h0Z" fill="currentColor"/></svg>',
            ],
            [
                'title' => 'Автомобиль',
                'slug' => 'car',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.0" id="Layer_1" viewBox="0 0 64 64" enable-background="new 0 0 64 64" xml:space="preserve" fill="none"><g id="SVGRepo_bgCarrier" stroke-width="0"/><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/><g id="SVGRepo_iconCarrier"> <path d="M60,28c0-8.301-5.016-24-24-24h-8C9.016,4,4,19.699,4,28c-2.211,0-4,1.789-4,4v16c0,2.211,1.789,4,4,4h4v4 c0,2.211,1.789,4,4,4h4c2.211,0,4-1.789,4-4v-4h24v4c0,2.211,1.789,4,4,4h4c2.211,0,4-1.789,4-4v-4h4c2.211,0,4-1.789,4-4V32 C64,29.789,62.211,28,60,28z M16,44c-2.211,0-4-1.789-4-4s1.789-4,4-4s4,1.789,4,4S18.211,44,16,44z M12,28c0-0.652,0.184-16,16-16 h8c15.41,0,15.984,14.379,16,16H12z M48,44c-2.211,0-4-1.789-4-4s1.789-4,4-4s4,1.789,4,4S50.211,44,48,44z" fill="currentColor"/> </g></svg>',
            ],
            [
                'title' => 'Кредит',
                'slug' => 'kredit',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 52 52" enable-background="new 0 0 52 52" xml:space="preserve"><g id="SVGRepo_bgCarrier" stroke-width="0"/><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/><g id="SVGRepo_iconCarrier"> <path d="M30.1,26.1c0-2.3,1.4-4.3,3.3-5.3c-0.3-3.9-3.5-6.9-7.5-6.9c-4.1,0-7.5,3.4-7.5,7.5c0,4.1,3.4,7.5,7.5,7.5 c1.5,0,3-0.5,4.1-1.3V26.1z" fill="currentColor"/> <path d="M30.1,36.4v-1.9c0-0.7,0.1-1.3,0.3-1.9H12.8c0-2.9-2.3-5.3-5.3-5.3V16.1c2.9,0,5.3-2.3,5.3-5.3h26.3 c0,2.9,2.3,5.3,5.3,5.3v4h3.8c0.7,0,1.3,0.1,1.9,0.3V9.7c0-2.5-2-4.5-4.5-4.5H6.5C4,5.2,2,7.2,2,9.7v24.2c0,2.5,2,4.5,4.5,4.5h24 C30.3,37.8,30.1,37.1,30.1,36.4z" fill="currentColor"/> <path d="M50,44.9c0,1-1,1.9-2,1.9H36c-1,0-1.9-0.9-1.9-1.9V43c0-1,0.9-1.9,1.9-1.9h12.1c1,0,1.9,0.9,1.9,1.9V44.9 L50,44.9z" fill="currentColor"/> <path d="M50,36.5c0,1-1,1.9-2,1.9H36c-1,0-1.9-0.9-1.9-1.9v-1.9c0-1,0.9-1.9,1.9-1.9h12.1c1,0,1.9,0.9,1.9,1.9V36.5 L50,36.5z" fill="currentColor"/> <g> <path d="M50,28c0,1-1,1.9-2,1.9H36c-1,0-1.9-0.9-1.9-1.9v-1.9c0-1,0.9-1.9,1.9-1.9h12.1c1,0,1.9,0.9,1.9,1.9V28 L50,28z" fill="currentColor"/> </g> </g></svg>',
            ],
            [
                'title' => 'Trade In',
                'slug' => 'trade-in',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 52 52" enable-background="new 0 0 52 52" xml:space="preserve"><g id="SVGRepo_bgCarrier" stroke-width="0"/><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/><g id="SVGRepo_iconCarrier"> <path d="M20,37.5c0-0.8-0.7-1.5-1.5-1.5h-15C2.7,36,2,36.7,2,37.5v11C2,49.3,2.7,50,3.5,50h15c0.8,0,1.5-0.7,1.5-1.5 V37.5z" fill="currentColor"/> <path d="M8.1,22H3.2c-1,0-1.5,0.9-0.9,1.4l8,8.3c0.4,0.3,1,0.3,1.4,0l8-8.3c0.6-0.6,0.1-1.4-0.9-1.4h-4.7 c0-5,4.9-10,9.9-10V6C15,6,8.1,13,8.1,22z" fill="currentColor"/> <path d="M41.8,20.3c-0.4-0.3-1-0.3-1.4,0l-8,8.3c-0.6,0.6-0.1,1.4,0.9,1.4h4.8c0,6-4.1,10-10.1,10v6 c9,0,16.1-7,16.1-16H49c1,0,1.5-0.9,0.9-1.4L41.8,20.3z" fill="currentColor"/> <path d="M50,3.5C50,2.7,49.3,2,48.5,2h-15C32.7,2,32,2.7,32,3.5v11c0,0.8,0.7,1.5,1.5,1.5h15c0.8,0,1.5-0.7,1.5-1.5 V3.5z" fill="currentColor"/> </g></svg>',
            ],
            [
                'title' => 'Сообщение',
                'slug' => 'message',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 52 52" enable-background="new 0 0 52 52" xml:space="preserve"><g id="SVGRepo_bgCarrier" stroke-width="0"/><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/><g id="SVGRepo_iconCarrier"> <path d="M26,4C12.7,4,2.1,13.8,2.1,25.9c0,3.8,1.1,7.4,2.9,10.6c0.3,0.5,0.4,1.1,0.2,1.7l-3.1,8.5 c-0.3,0.8,0.5,1.5,1.3,1.3l8.6-3.3c0.5-0.2,1.1-0.1,1.7,0.2c3.6,2,7.9,3.2,12.5,3.2C39.3,48,50,38.3,50,26.1C49.9,13.8,39.2,4,26,4z M14,30c-2.2,0-4-1.8-4-4s1.8-4,4-4s4,1.8,4,4S16.2,30,14,30z M26,30c-2.2,0-4-1.8-4-4s1.8-4,4-4s4,1.8,4,4S28.2,30,26,30z M38,30 c-2.2,0-4-1.8-4-4s1.8-4,4-4s4,1.8,4,4S40.2,30,38,30z" fill="currentColor"/> </g></svg>',
            ],
            [
                'title' => 'Комментарии',
                'slug' => 'comments',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 52 52" enable-background="new 0 0 52 52" xml:space="preserve"><g id="SVGRepo_bgCarrier" stroke-width="0"/><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/><g id="SVGRepo_iconCarrier"> <g> <path d="M47.8,31c-0.1-0.4-0.1-0.8,0.1-1.2c1.3-2.3,2.1-4.9,2.1-7.7c0-8.8-7.6-16-17-16c-4.4,0-8.4,1.6-11.4,4.2 C31.9,11.5,40,19.9,40,30.1c0,2.5-0.5,4.9-1.4,7.1c1.1-0.4,2.2-0.9,3.2-1.4c0.4-0.2,0.8-0.3,1.2-0.1l6.1,2.4 c0.6,0.2,1.1-0.3,0.9-0.9L47.8,31z" fill="currentColor"/> <g> <path d="M19,14.1c-9.4,0-17,7.2-17,16c0,2.8,0.8,5.4,2.1,7.7c0.2,0.4,0.3,0.8,0.1,1.2L2,45.1 c-0.2,0.6,0.3,1.1,0.9,0.9L9,43.6c0.4-0.1,0.8-0.1,1.2,0.1c2.6,1.5,5.6,2.3,8.8,2.3c9.4,0,17-7.2,17-16C36,21.3,28.4,14.1,19,14.1 z" fill="currentColor"/> </g> </g> </g></svg>',
            ],
            [
                'title' => 'Поддержка',
                'slug' => 'support',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"><g id="SVGRepo_bgCarrier" stroke-width="0"/><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/><g id="SVGRepo_iconCarrier"> <title>support</title> <rect fill="currentColor"/> <path d="M12,2a8,8,0,0,0-8,8v1.9A2.92,2.92,0,0,0,3,14a2.88,2.88,0,0,0,1.94,2.61C6.24,19.72,8.85,22,12,22h3V20H12c-2.26,0-4.31-1.7-5.34-4.39l-.21-.55L5.86,15A1,1,0,0,1,5,14a1,1,0,0,1,.5-.86l.5-.29V11a1,1,0,0,1,1-1H17a1,1,0,0,1,1,1v5H13.91a1.5,1.5,0,1,0-1.52,2H20a2,2,0,0,0,2-2V14a2,2,0,0,0-2-2V10A8,8,0,0,0,12,2Z" fill="currentColor"/> </g></svg>',
            ],
            [
                'title' => 'Локация',
                'slug' => 'lokaciia',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 52 52" enable-background="new 0 0 52 52" xml:space="preserve"><g id="SVGRepo_bgCarrier" stroke-width="0"/><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/><g id="SVGRepo_iconCarrier"> <path d="M26,2C15.5,2,7,10.5,7,21.1c0,13.2,13.6,25.3,17.8,28.5c0.7,0.6,1.7,0.6,2.5,0C31.5,46.3,45,34.3,45,21.1 C45,10.5,36.5,2,26,2z M26,29c-4.4,0-8-3.6-8-8s3.6-8,8-8s8,3.6,8,8S30.4,29,26,29z" fill="currentColor"/> </g></svg>',
            ],
        ];
    }
}
