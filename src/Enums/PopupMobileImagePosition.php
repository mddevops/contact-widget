<?php

namespace SiteApps\ContactWidget\Enums;

use Filament\Support\Contracts\HasLabel;

enum PopupMobileImagePosition: string implements HasLabel
{
    case Top = 'top';
    case Bottom = 'bottom';

    public function getLabel(): string
    {
        return match ($this) {
            self::Top => 'Сверху блока',
            self::Bottom => 'Снизу блока',
        };
    }

    public function layoutClass(): string
    {
        return 'cbp-layout--mobile-' . $this->value;
    }
}
