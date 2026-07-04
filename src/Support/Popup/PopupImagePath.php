<?php

namespace SiteApps\ContactWidget\Support\Popup;

use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PopupImagePath
{
    public static function normalizeForStorage(mixed $image): ?string
    {
        return self::normalize($image);
    }

    /**
     * Normalize popup image state to a public-disk path (e.g. popups/file.jpg).
     */
    public static function normalize(mixed $image): ?string
    {
        if (blank($image)) {
            return null;
        }

        $path = self::extract($image);

        if ($path instanceof TemporaryUploadedFile) {
            return self::storeTemporaryFile($path);
        }

        if (! is_string($path) || $path === '') {
            return null;
        }

        if (TemporaryUploadedFile::canUnserialize($path)) {
            $unserialized = TemporaryUploadedFile::unserializeFromLivewireRequest($path);

            if ($unserialized instanceof TemporaryUploadedFile) {
                return self::storeTemporaryFile($unserialized);
            }

            if (is_array($unserialized)) {
                foreach ($unserialized as $file) {
                    if ($file instanceof TemporaryUploadedFile) {
                        return self::storeTemporaryFile($file);
                    }
                }
            }
        }

        if (str_starts_with($path, 'livewire-tmp/') || str_starts_with($path, 'livewire-file:')) {
            $tmpPath = str_starts_with($path, 'livewire-file:')
                ? (string) str($path)->after('livewire-file:')
                : $path;

            try {
                return self::storeTemporaryFile(TemporaryUploadedFile::createFromLivewire($tmpPath));
            } catch (\Throwable) {
                return null;
            }
        }

        $path = self::cleanStoredPath($path);

        if ($path === null) {
            return null;
        }

        if (Storage::disk('public')->exists($path)) {
            return $path;
        }

        return $path;
    }

    /**
     * Resolve a stored DB path to a public URL, or null when invalid / missing.
     */
    public static function url(?string $path): ?string
    {
        $stored = self::cleanStoredPath($path);

        if ($stored === null) {
            return null;
        }

        if (! Storage::disk('public')->exists($stored)) {
            return null;
        }

        return Storage::disk('public')->url($stored);
    }

    /**
     * Resolve form/upload state to a preview URL (admin live preview).
     */
    public static function previewUrl(mixed $image): ?string
    {
        if (blank($image)) {
            return null;
        }

        $path = self::extract($image);

        if ($path instanceof TemporaryUploadedFile) {
            try {
                return $path->temporaryUrl();
            } catch (\Throwable) {
                return null;
            }
        }

        if (! is_string($path) || $path === '') {
            return null;
        }

        if (TemporaryUploadedFile::canUnserialize($path)) {
            $unserialized = TemporaryUploadedFile::unserializeFromLivewireRequest($path);

            if ($unserialized instanceof TemporaryUploadedFile) {
                try {
                    return $unserialized->temporaryUrl();
                } catch (\Throwable) {
                    return null;
                }
            }
        }

        if (str_starts_with($path, 'livewire-tmp/') || str_starts_with($path, 'livewire-file:')) {
            $tmpPath = str_starts_with($path, 'livewire-file:')
                ? (string) str($path)->after('livewire-file:')
                : $path;

            try {
                return TemporaryUploadedFile::createFromLivewire($tmpPath)->temporaryUrl();
            } catch (\Throwable) {
                return null;
            }
        }

        return self::url($path);
    }

    /**
     * @return mixed TemporaryUploadedFile|string|null
     */
    public static function extract(mixed $image): mixed
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

        if (! is_array($image)) {
            return null;
        }

        foreach ($image as $value) {
            $path = self::extract($value);

            if ($path !== null && $path !== '') {
                return $path;
            }
        }

        return null;
    }

    public static function cleanStoredPath(?string $path): ?string
    {
        if (! filled($path) || ! is_string($path)) {
            return null;
        }

        $path = trim($path);

        if ($path === '') {
            return null;
        }

        if (str_starts_with($path, 'livewire-file:')) {
            return null;
        }

        $path = ltrim($path, '/');

        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        return $path !== '' ? $path : null;
    }

    protected static function storeTemporaryFile(TemporaryUploadedFile $file): ?string
    {
        try {
            if (! $file->exists()) {
                return null;
            }

            $stored = $file->store('popups', 'public');

            return is_string($stored) && $stored !== '' ? $stored : null;
        } catch (\Throwable) {
            return null;
        }
    }
}
