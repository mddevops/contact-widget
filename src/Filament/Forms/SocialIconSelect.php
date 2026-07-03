<?php

namespace SiteApps\ContactWidget\Filament\Forms;

use SiteApps\ContactWidget\Models\SocialIcon;
use Filament\Forms\Components\Select;

class SocialIconSelect
{
    public static function make(string $name): Select
    {
        return Select::make($name)
            ->options(fn (): array => self::options())
            ->getOptionLabelUsing(fn (mixed $value): ?string => self::labelFor($value))
            ->getSearchResultsUsing(fn (string $search): array => self::search($search))
            ->allowHtml()
            ->searchable()
            ->preload();
    }

    /**
     * @return array<int|string, string>
     */
    public static function options(): array
    {
        return SocialIcon::query()
            ->orderBy('title')
            ->get()
            ->mapWithKeys(fn (SocialIcon $icon): array => [$icon->id => $icon->selectOptionHtml()])
            ->all();
    }

    public static function labelFor(mixed $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        $icon = SocialIcon::query()->find($value);

        return $icon?->selectOptionHtml();
    }

    /**
     * @return array<int|string, string>
     */
    public static function search(string $search): array
    {
        return SocialIcon::query()
            ->where('title', 'like', '%'.$search.'%')
            ->orderBy('title')
            ->limit(50)
            ->get()
            ->mapWithKeys(fn (SocialIcon $icon): array => [$icon->id => $icon->selectOptionHtml()])
            ->all();
    }
}
