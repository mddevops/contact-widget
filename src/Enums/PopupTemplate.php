<?php

namespace SiteApps\ContactWidget\Enums;

use Filament\Support\Contracts\HasLabel;

enum PopupTemplate: string implements HasLabel
{
    case LeadForm = 'lead_form';
    case Callback = 'callback';
    case Discount = 'discount';
    case TestDrive = 'test_drive';
    case TradeIn = 'trade_in';

    public function getLabel(): string
    {
        return match ($this) {
            self::LeadForm => 'Лид форма',
            self::Callback => 'Обратный звонок',
            self::Discount => 'Скидка',
            self::TestDrive => 'Тест драйв',
            self::TradeIn => 'Трейд-ин',
        };
    }
}
