<?php

namespace SiteApps\ContactWidget\Filament\Resources\PopupResource\Pages;

use SiteApps\ContactWidget\Filament\Resources\PopupResource;
use SiteApps\ContactWidget\Filament\Resources\PopupResource\Concerns\HasPopupPreviewImage;
use SiteApps\ContactWidget\Filament\Resources\PopupResource\Concerns\ManagesPopupBuilderLayout;
use SiteApps\ContactWidget\Filament\Resources\PopupResource\Concerns\ManagesPopupFormData;
use Filament\Resources\Pages\EditRecord;

class EditPopup extends EditRecord
{
    use HasPopupPreviewImage;
    use ManagesPopupBuilderLayout;
    use ManagesPopupFormData;

    protected static string $resource = PopupResource::class;

    protected static string $view = 'contact-widget::filament.resources.popup-resource.pages.builder';

    public function getTitle(): string
    {
        return 'Редактировать попап';
    }

}
