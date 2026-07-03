<?php

namespace SiteApps\ContactWidget\Filament\Resources\PopupResource\Pages;

use SiteApps\ContactWidget\Filament\Resources\PopupResource;
use SiteApps\ContactWidget\Filament\Resources\PopupResource\Concerns\HasPopupPreviewImage;
use SiteApps\ContactWidget\Filament\Resources\PopupResource\Concerns\ManagesPopupBuilderLayout;
use SiteApps\ContactWidget\Filament\Resources\PopupResource\Concerns\ManagesPopupFormData;
use SiteApps\ContactWidget\Support\Popup\PopupDisplayRules;
use SiteApps\ContactWidget\Support\Popup\PopupSettings;
use Filament\Resources\Pages\CreateRecord;

class CreatePopup extends CreateRecord
{
    use HasPopupPreviewImage;
    use ManagesPopupBuilderLayout;
    use ManagesPopupFormData;

    protected static string $resource = PopupResource::class;

    protected static string $view = 'contact-widget::filament.resources.popup-resource.pages.builder';

    public function mount(): void
    {
        parent::mount();

        $this->form->fill([
            'title' => 'Остались вопросы?',
            'subtitle' => 'Оставьте номер и мы перезвоним',
            'settings' => PopupSettings::defaults(),
            'display_rules' => PopupDisplayRules::defaults(),
            'is_active' => true,
            'button_text' => 'Отправить заявку',
        ]);
    }

    public function getTitle(): string
    {
        return 'Создать попап';
    }
}
