<?php

namespace SiteApps\ContactWidget\Filament\Resources\PopupResource\Concerns;

use SiteApps\ContactWidget\Support\Popup\PopupDisplayRules;
use SiteApps\ContactWidget\Support\Popup\PopupImagePath;
use SiteApps\ContactWidget\Support\Popup\PopupSettings;

trait ManagesPopupFormData
{
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['settings'] = PopupSettings::merge($data['settings'] ?? []);
        $data['display_rules'] = PopupDisplayRules::merge($data['display_rules'] ?? []);

        return $data;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->normalizePopupFormData($data);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->normalizePopupFormData($data);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function normalizePopupFormData(array $data): array
    {
        $existingSettings = is_array($this->record?->settings) ? $this->record->settings : [];
        $existingRules = is_array($this->record?->display_rules) ? $this->record->display_rules : [];
        $submittedSettings = is_array($data['settings'] ?? null) ? $data['settings'] : [];
        $submittedRules = is_array($data['display_rules'] ?? null) ? $data['display_rules'] : [];

        $data['settings'] = PopupSettings::merge(array_replace($existingSettings, $submittedSettings));
        $data['display_rules'] = PopupDisplayRules::merge(array_replace($existingRules, $submittedRules));

        if (array_key_exists('image', $data)) {
            $data['image'] = PopupImagePath::normalizeForStorage($data['image']);
        }

        return $data;
    }
}
