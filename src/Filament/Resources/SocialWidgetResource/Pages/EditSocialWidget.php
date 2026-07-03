<?php

namespace SiteApps\ContactWidget\Filament\Resources\SocialWidgetResource\Pages;

use SiteApps\ContactWidget\Filament\Resources\SocialWidgetResource;
use SiteApps\ContactWidget\Filament\Resources\SocialWidgetResource\Concerns\HasSocialWidgetPreview;
use SiteApps\ContactWidget\Filament\Resources\SocialWidgetResource\Concerns\ManagesSocialWidgetOnSiteDisplay;
use SiteApps\ContactWidget\Filament\Resources\SocialWidgetResource\Concerns\ManagesSocialWidgetBuilderLayout;
use SiteApps\ContactWidget\Filament\Resources\SocialWidgetResource\Concerns\ManagesSocialWidgetFormData;
use Filament\Resources\Pages\EditRecord;

class EditSocialWidget extends EditRecord
{
    use HasSocialWidgetPreview;
    use ManagesSocialWidgetBuilderLayout;
    use ManagesSocialWidgetFormData;
    use ManagesSocialWidgetOnSiteDisplay;

    protected static string $resource = SocialWidgetResource::class;

    protected static string $view = 'contact-widget::filament.resources.social-widget-resource.pages.builder';

    public function getTitle(): string
    {
        return 'Редактировать виджет';
    }
}
