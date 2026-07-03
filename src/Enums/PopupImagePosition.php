<?php

namespace SiteApps\ContactWidget\Enums;

use Filament\Support\Contracts\HasLabel;

enum PopupImagePosition: string implements HasLabel
{
    case Left = 'left';
    case Right = 'right';

    public function getLabel(): string
    {
        return match ($this) {
            self::Left => 'Слева',
            self::Right => 'Справа',
        };
    }

    public function layoutClass(): string
    {
        return 'cbp-layout--' . $this->value;
    }
}
