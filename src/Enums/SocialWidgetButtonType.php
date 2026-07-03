<?php

namespace SiteApps\ContactWidget\Enums;

use Filament\Support\Contracts\HasLabel;

enum SocialWidgetButtonType: string implements HasLabel
{
    case Phone = 'phone';
    case Callback = 'callback';
    case Whatsapp = 'whatsapp';
    case Telegram = 'telegram';
    case Max = 'max';
    case TestDrive = 'test_drive';
    case Credit = 'credit';
    case TradeIn = 'trade_in';
    case Custom = 'custom';

    public function getLabel(): string
    {
        return match ($this) {
            self::Phone => 'Телефон',
            self::Callback => 'Обратный звонок',
            self::Whatsapp => 'WhatsApp',
            self::Telegram => 'Telegram',
            self::Max => 'Max',
            self::TestDrive => 'Тест-драйв',
            self::Credit => 'Кредит',
            self::TradeIn => 'Trade In',
            self::Custom => 'Своя кнопка',
        };
    }
}
