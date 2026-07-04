<?php

namespace SiteApps\ContactWidget\Filament\Resources\SocialWidgetResource\Concerns;

use SiteApps\ContactWidget\Support\Social\SocialWidgetMobileSettings;
use SiteApps\ContactWidget\Models\SocialWidget;
use SiteApps\ContactWidget\Services\Social\SocialWidgetService;

trait ManagesSocialWidgetFormData
{
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['mobile_settings'] = SocialWidgetMobileSettings::merge($data['mobile_settings'] ?? null);

        return $data;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->normalizeSocialWidgetFormData($data);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->normalizeSocialWidgetFormData($data);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function normalizeSocialWidgetFormData(array $data): array
    {
        $data['mobile_settings'] = SocialWidgetMobileSettings::merge($data['mobile_settings'] ?? null);
        $data['enabled'] = (bool) ($data['show_on_site'] ?? false);

        if ($data['show_on_site'] ?? false) {
            SocialWidget::clearOnSiteExcept($this->record?->id);
        }

        return $data;
    }

    protected function afterSave(): void
    {
        SocialWidgetService::forgetCache();
    }

    protected function afterCreate(): void
    {
        SocialWidgetService::forgetCache();
    }

    protected function afterDelete(): void
    {
        SocialWidgetService::forgetCache();
    }
}
