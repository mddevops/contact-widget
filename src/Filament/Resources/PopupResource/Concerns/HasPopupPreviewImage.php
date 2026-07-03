<?php

namespace SiteApps\ContactWidget\Filament\Resources\PopupResource\Concerns;

use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

trait HasPopupPreviewImage
{
    public function getPopupPreviewImageUrl(): ?string
    {
        $image = $this->data['image'] ?? null;

        if (is_array($image) && $image !== []) {
            $path = array_values($image)[0] ?? null;

            if ($path instanceof TemporaryUploadedFile) {
                return $path->temporaryUrl();
            }

            if (is_string($path) && $path !== '') {
                return Storage::disk('public')->url($path);
            }
        }

        if (is_string($image) && $image !== '') {
            return Storage::disk('public')->url($image);
        }

        return null;
    }
}
