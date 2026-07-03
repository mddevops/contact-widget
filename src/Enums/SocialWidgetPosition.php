<?php

namespace SiteApps\ContactWidget\Enums;

use Filament\Support\Contracts\HasLabel;

enum SocialWidgetPosition: string implements HasLabel
{
    case Left = 'left';
    case Right = 'right';
    case Center = 'center';

    public function getLabel(): string
    {
        return match ($this) {
            self::Left => 'Слева',
            self::Right => 'Справа',
            self::Center => 'По центру',
        };
    }
}
