<?php

namespace SiteApps\ContactWidget\Services;

use SiteApps\ContactWidget\Enums\PopupDisplayMode;
use SiteApps\ContactWidget\Models\Popup;
use SiteApps\ContactWidget\Support\Popup\PopupDisplayRules;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class PopupService
{
    public const CACHE_KEY = 'popups_active';

    public static function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * @return Collection<int, Popup>
     */
    public static function active(): Collection
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return Popup::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        });
    }

    public static function normalizePath(?string $path): string
    {
        $path = trim((string) $path);

        if ($path === '' || $path === '/') {
            return '';
        }

        if (str_contains($path, '://')) {
            $path = parse_url($path, PHP_URL_PATH) ?? '';
        } elseif (preg_match('#^[^/]+\.[^/]+(/.*)?$#', $path, $matches) === 1) {
            $path = $matches[1] ?? '';
        }

        return strtolower(trim($path, '/'));
    }

    /**
     * @return Collection<int, Popup>
     */
    public static function forPath(?string $path = null): Collection
    {
        $normalizedPath = self::normalizePath($path ?? request()?->path());

        return self::active()
            ->filter(fn (Popup $popup) => self::matchesPath($popup, $normalizedPath))
            ->values();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function queuePayload(?string $path = null): array
    {
        return self::forPath($path)
            ->reject(fn (Popup $popup) => PopupDisplayRules::isManualOnly($popup->resolvedDisplayRules()))
            ->map(fn (Popup $popup): array => self::popupQueueItem($popup))
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    public static function popupQueueItem(Popup $popup): array
    {
        $rules = $popup->resolvedDisplayRules();
        $schedule = $rules['schedule'] ?? [];

        return [
            'id' => $popup->id,
            'mode' => $rules['mode'] ?? 'all_pages',
            'trigger' => $rules['trigger'] ?? 'delay',
            'delay' => (int) ($rules['delay'] ?? 5),
            'scrollPercent' => (int) ($rules['scroll_percent'] ?? 50),
            'frequency' => $rules['frequency'] ?? 'visit',
            'sessionLimit' => (int) ($rules['session_limit'] ?? 1),
            'schedule' => [
                'enabled' => (bool) ($schedule['enabled'] ?? false),
                'days' => array_values($schedule['days'] ?? []),
                'from' => (string) ($schedule['from'] ?? '00:00'),
                'to' => (string) ($schedule['to'] ?? '23:59'),
            ],
            'sortOrder' => (int) $popup->sort_order,
        ];
    }

    public static function matchesPath(Popup $popup, ?string $path = null): bool
    {
        $rules = $popup->resolvedDisplayRules();
        $mode = $rules['mode'] ?? PopupDisplayMode::AllPages->value;
        $currentPath = self::normalizePath($path ?? request()?->path());

        if (in_array($mode, [PopupDisplayMode::AllPages->value, PopupDisplayMode::ExitIntent->value], true)) {
            return true;
        }

        if ($mode !== PopupDisplayMode::SelectedPages->value) {
            return false;
        }

        $urlPaths = $rules['url_paths'] ?? [];

        foreach ($urlPaths as $basePath) {
            $basePath = self::normalizePath($basePath);

            if ($currentPath === $basePath) {
                return true;
            }

            if (($rules['include_subpages'] ?? false) && $basePath !== '' && str_starts_with($currentPath, $basePath . '/')) {
                return true;
            }
        }

        return false;
    }
}
