<?php

namespace SiteApps\ContactWidget\Enums;

use Filament\Support\Contracts\HasLabel;

enum PopupDisplayMode: string implements HasLabel
{
    case AllPages = 'all_pages';
    case SelectedPages = 'selected_pages';
    case ExitIntent = 'exit_intent';
    case ManualOnly = 'manual_only';

    public function getLabel(): string
    {
        return match ($this) {
            self::AllPages => 'Все страницы',
            self::SelectedPages => 'Выбранные URL',
            self::ExitIntent => 'При уходе с сайта',
            self::ManualOnly => 'Только по кнопке',
        };
    }
}
