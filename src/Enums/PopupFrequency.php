<?php

namespace SiteApps\ContactWidget\Enums;

use Filament\Support\Contracts\HasLabel;

enum PopupFrequency: string implements HasLabel
{
    case Visit = 'visit';
    case Daily = 'daily';
    case Weekly = 'weekly';
    case Once = 'once';

    public function getLabel(): string
    {
        return match ($this) {
            self::Visit => 'Каждый визит',
            self::Daily => 'Раз в сутки',
            self::Weekly => 'Раз в неделю',
            self::Once => 'Только один раз',
        };
    }
}
