<?php

namespace SiteApps\ContactWidget\Filament\Resources\SocialWidgetResource\Concerns;

use SiteApps\ContactWidget\Models\Popup;
use SiteApps\ContactWidget\Models\SocialIcon;

trait HasSocialWidgetPreview
{
    /**
     * @return array<int, array{title: string, svg: string}>
     */
    public function getIconsForPreview(): array
    {
        return SocialIcon::query()
            ->orderBy('title')
            ->get(['id', 'title', 'svg'])
            ->keyBy('id')
            ->map(fn (SocialIcon $icon): array => [
                'title' => $icon->title,
                'svg' => $icon->svg,
            ])
            ->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getButtonsForPreview(): array
    {
        $state = $this->form->getRawState();
        $buttons = $state['buttons'] ?? null;

        if (is_array($buttons) && $buttons !== []) {
            return array_values($buttons);
        }

        if (! $this->record) {
            return [];
        }

        return $this->record->buttons()
            ->orderBy('sort')
            ->get()
            ->map(fn ($button) => $button->toArray())
            ->all();
    }

    /**
     * @return array<int, string>
     */
    public function getPopupsForPreview(): array
    {
        return Popup::query()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }
}
