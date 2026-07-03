<?php

namespace SiteApps\ContactWidget\Enums;

use Filament\Support\Contracts\HasLabel;

enum PopupTriggerType: string implements HasLabel
{
    case Delay = 'delay';
    case Scroll = 'scroll';

    public function getLabel(): string
    {
        return match ($this) {
            self::Delay => 'По задержке',
            self::Scroll => 'По скроллу',
        };
    }
}
