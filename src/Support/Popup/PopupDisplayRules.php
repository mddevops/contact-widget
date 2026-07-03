<?php

namespace SiteApps\ContactWidget\Support\Popup;

use SiteApps\ContactWidget\Enums\PopupDisplayMode;
use SiteApps\ContactWidget\Enums\PopupTriggerType;
use SiteApps\ContactWidget\Services\PopupService;

class PopupDisplayRules
{
    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'mode' => PopupDisplayMode::AllPages->value,
            'url_paths' => [],
            'include_subpages' => false,
            'trigger' => PopupTriggerType::Delay->value,
            'delay' => 5,
            'scroll_percent' => 50,
            'frequency' => 'visit',
            'session_limit' => 1,
            'schedule' => [
                'enabled' => false,
                'days' => [1, 2, 3, 4, 5, 6, 7],
                'from' => '00:00',
                'to' => '23:59',
            ],
        ];
    }

    /**
     * @param  array<string, mixed>|null  $rules
     * @return array<string, mixed>
     */
    public static function merge(?array $rules): array
    {
        $merged = array_replace(self::defaults(), $rules ?? []);

        if (($merged['exit_intent'] ?? false) && $merged['mode'] === PopupDisplayMode::AllPages->value) {
            $merged['mode'] = PopupDisplayMode::ExitIntent->value;
        }

        unset($merged['exit_intent'], $merged['page_ids']);

        $merged['url_paths'] = collect($merged['url_paths'] ?? [])
            ->map(fn ($path) => PopupService::normalizePath(is_string($path) ? $path : ''))
            ->unique()
            ->values()
            ->all();

        $merged['include_subpages'] = (bool) ($merged['include_subpages'] ?? false);
        $merged['delay'] = max(0, min(300, (int) ($merged['delay'] ?? 0)));
        $merged['scroll_percent'] = max(1, min(100, (int) ($merged['scroll_percent'] ?? 50)));
        $merged['session_limit'] = max(1, min(99, (int) ($merged['session_limit'] ?? 1)));

        if (! in_array($merged['trigger'] ?? '', [
            PopupTriggerType::Delay->value,
            PopupTriggerType::Scroll->value,
        ], true)) {
            $merged['trigger'] = PopupTriggerType::Delay->value;
        }

        if (! in_array($merged['mode'], [
            PopupDisplayMode::AllPages->value,
            PopupDisplayMode::SelectedPages->value,
            PopupDisplayMode::ExitIntent->value,
            PopupDisplayMode::ManualOnly->value,
        ], true)) {
            $merged['mode'] = PopupDisplayMode::AllPages->value;
        }

        if ($merged['mode'] !== PopupDisplayMode::SelectedPages->value) {
            $merged['include_subpages'] = false;
        }

        if (in_array($merged['mode'], [
            PopupDisplayMode::ExitIntent->value,
            PopupDisplayMode::ManualOnly->value,
        ], true)) {
            $merged['trigger'] = PopupTriggerType::Delay->value;
        }

        $merged['schedule'] = self::mergeSchedule($merged['schedule'] ?? null);

        return $merged;
    }

    /**
     * @param  array<string, mixed>|null  $schedule
     * @return array{enabled: bool, days: list<int>, from: string, to: string}
     */
    public static function mergeSchedule(?array $schedule): array
    {
        $defaults = self::defaults()['schedule'];

        if (! is_array($schedule) || ! array_key_exists('enabled', $schedule)) {
            return $defaults;
        }

        $days = collect($schedule['days'] ?? [])
            ->map(fn ($day) => (int) $day)
            ->filter(fn (int $day) => $day >= 1 && $day <= 7)
            ->unique()
            ->sort()
            ->values()
            ->all();

        if ($days === []) {
            $days = $defaults['days'];
        }

        return [
            'enabled' => (bool) ($schedule['enabled'] ?? false),
            'days' => $days,
            'from' => self::normalizeTime((string) ($schedule['from'] ?? $defaults['from']), $defaults['from']),
            'to' => self::normalizeTime((string) ($schedule['to'] ?? $defaults['to']), $defaults['to']),
        ];
    }

    public static function normalizeTime(string $value, string $fallback): string
    {
        if (preg_match('/^(\d{1,2}):(\d{2})$/', trim($value), $matches) !== 1) {
            return $fallback;
        }

        $hours = max(0, min(23, (int) $matches[1]));
        $minutes = max(0, min(59, (int) $matches[2]));

        return sprintf('%02d:%02d', $hours, $minutes);
    }

    public static function displayModeLabel(?array $rules): string
    {
        $mode = self::merge($rules)['mode'] ?? PopupDisplayMode::AllPages->value;

        return PopupDisplayMode::tryFrom($mode)?->getLabel() ?? 'Все страницы';
    }

    public static function conditionsSummary(?array $rules): string
    {
        $rules = self::merge($rules);

        if ($rules['mode'] === PopupDisplayMode::ManualOnly->value) {
            return 'Только по кнопке виджета';
        }

        $parts = [self::displayModeLabel($rules)];

        if ($rules['mode'] === PopupDisplayMode::SelectedPages->value) {
            $paths = $rules['url_paths'] ?? [];

            if ($paths !== []) {
                $parts[] = implode(', ', array_slice($paths, 0, 3)) . (count($paths) > 3 ? '…' : '');
            }

            if ($rules['include_subpages'] ?? false) {
                $parts[] = 'с подстраницами';
            }
        }

        if ($rules['mode'] === PopupDisplayMode::ExitIntent->value) {
            $parts[] = 'при уходе';
        } elseif (($rules['trigger'] ?? PopupTriggerType::Delay->value) === PopupTriggerType::Scroll->value) {
            $parts[] = 'скролл: ' . ($rules['scroll_percent'] ?? 50) . '%';
        } else {
            $parts[] = 'задержка: ' . ($rules['delay'] ?? 0) . 'с';
        }

        $frequency = match ($rules['frequency'] ?? 'visit') {
            'daily' => 'раз в сутки',
            'weekly' => 'раз в неделю',
            'once' => 'один раз',
            default => 'каждый визит',
        };

        $parts[] = $frequency;

        if (($rules['session_limit'] ?? 1) > 1) {
            $parts[] = 'до ' . $rules['session_limit'] . ' раз/сессию';
        }

        $schedule = $rules['schedule'] ?? [];

        if (($schedule['enabled'] ?? false) === true) {
            $parts[] = 'по расписанию';
        }

        return implode(' · ', $parts);
    }

    public static function isExitIntent(?array $rules): bool
    {
        return self::merge($rules)['mode'] === PopupDisplayMode::ExitIntent->value;
    }

    public static function isManualOnly(?array $rules): bool
    {
        return self::merge($rules)['mode'] === PopupDisplayMode::ManualOnly->value;
    }
}
