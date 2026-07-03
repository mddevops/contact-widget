<?php

namespace SiteApps\ContactWidget\Enums;

use Filament\Support\Contracts\HasLabel;

enum PopupListMarkerStyle: string implements HasLabel
{
    case Check = 'check';
    case Arrow = 'arrow';
    case Circle = 'circle';
    case CircleFilled = 'circle-filled';
    case Star = 'star';
    case Heart = 'heart';

    public function getLabel(): string
    {
        return match ($this) {
            self::Check => 'Галочка',
            self::Arrow => 'Стрелка',
            self::Circle => 'Круг',
            self::CircleFilled => 'Заполненный круг',
            self::Star => 'Звезда',
            self::Heart => 'Сердце',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Check => '✓',
            self::Arrow => '→',
            self::Circle => '○',
            self::CircleFilled => '●',
            self::Star => '★',
            self::Heart => '♥',
        };
    }
}
