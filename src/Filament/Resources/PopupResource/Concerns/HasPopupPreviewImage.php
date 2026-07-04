<?php

namespace SiteApps\ContactWidget\Filament\Resources\PopupResource\Concerns;

use SiteApps\ContactWidget\Support\Popup\PopupImagePath;

trait HasPopupPreviewImage
{
    public ?string $previewImageUrl = null;

    protected function afterFill(): void
    {
        $this->refreshPreviewImageUrl();
    }

    public function refreshPreviewImageUrl(): void
    {
        $this->previewImageUrl = $this->getPopupPreviewImageUrl();
    }

    public function updatedDataImage(mixed $value): void
    {
        if (blank(data_get($this->form->getRawState(), 'image'))) {
            $this->previewImageUrl = null;

            return;
        }

        $this->refreshPreviewImageUrl();
    }

    public function getPopupPreviewImageUrl(): ?string
    {
        $image = data_get($this->form->getRawState(), 'image');

        return PopupImagePath::previewUrl($image);
    }
}
