<?php

namespace SiteApps\ContactWidget\Enums;

use Filament\Support\Contracts\HasLabel;

enum SocialWidgetOpenDirection: string implements HasLabel
{
    case Up = 'up';
    case Down = 'down';

    public function getLabel(): string
    {
        return match ($this) {
            self::Up => 'Вверх',
            self::Down => 'Вниз',
        };
    }
}
