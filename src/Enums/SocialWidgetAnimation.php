<?php

namespace SiteApps\ContactWidget\Enums;

use Filament\Support\Contracts\HasLabel;

enum SocialWidgetAnimation: string implements HasLabel
{
    case None = 'none';
    case Pulse = 'pulse';
    case Shake = 'shake';
    case Jump = 'jump';

    public function getLabel(): string
    {
        return match ($this) {
            self::None => 'Без анимации',
            self::Pulse => 'Пульсация',
            self::Shake => 'Тряска',
            self::Jump => 'Прыжок',
        };
    }
}
