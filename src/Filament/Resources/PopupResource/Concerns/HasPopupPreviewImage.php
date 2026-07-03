<?php

namespace SiteApps\ContactWidget\Filament\Resources\PopupResource\Concerns;

use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

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

        return $this->resolveImageUrl($this->extractImagePath($image));
    }

    /**
     * @param  mixed  $image
     */
    protected function extractImagePath(mixed $image): mixed
    {
        if ($image instanceof TemporaryUploadedFile) {
            return $image;
        }

        if (is_string($image)) {
            if (TemporaryUploadedFile::canUnserialize($image)) {
                return TemporaryUploadedFile::unserializeFromLivewireRequest($image);
            }

            return $image;
        }

        if (! is_array($image) || $image === []) {
            return null;
        }

        foreach ($image as $value) {
            $path = $this->extractImagePath($value);

            if ($path !== null && $path !== '') {
                return $path;
            }
        }

        return null;
    }

    protected function resolveImageUrl(mixed $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        if ($path instanceof TemporaryUploadedFile) {
            try {
                return $path->temporaryUrl();
            } catch (\Throwable) {
                return null;
            }
        }

        if (! is_string($path)) {
            return null;
        }

        if (TemporaryUploadedFile::canUnserialize($path)) {
            return $this->resolveImageUrl(TemporaryUploadedFile::unserializeFromLivewireRequest($path));
        }

        if (str_starts_with($path, 'livewire-tmp/')) {
            try {
                return TemporaryUploadedFile::createFromLivewire($path)->temporaryUrl();
            } catch (\Throwable) {
                // Fall through to storage checks.
            }
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        $normalized = ltrim($path, '/');

        if (str_starts_with($normalized, 'storage/')) {
            return url('/'.$normalized);
        }

        return Storage::disk('public')->url($normalized);
    }
}
