<?php

namespace SiteApps\ContactWidget\Enums;

use Filament\Support\Contracts\HasLabel;

enum SocialWidgetButtonOpenType: string implements HasLabel
{
    case Url = 'url';
    case Phone = 'phone';
    case Popup = 'popup';

    public function getLabel(): string
    {
        return match ($this) {
            self::Url => 'Ссылка',
            self::Phone => 'Телефон',
            self::Popup => 'Попап',
        };
    }
}
