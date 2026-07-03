<?php

namespace SiteApps\ContactWidget\Filament\Resources\SocialWidgetResource\Pages;

use SiteApps\ContactWidget\Filament\Resources\SocialWidgetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSocialWidgets extends ListRecords
{
    protected static string $resource = SocialWidgetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
