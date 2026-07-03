<?php

namespace SiteApps\ContactWidget\Filament\Resources\SocialWidgetResource\Concerns;

use Filament\Actions\Action;

trait ManagesSocialWidgetOnSiteDisplay
{
    public function confirmReplaceOnSiteAction(): Action
    {
        return Action::make('confirmReplaceOnSite')
            ->modalIcon('heroicon-o-arrow-path-rounded-square')
            ->modalHeading('Заменить виджет на сайте?')
            ->modalDescription(function (array $arguments): string {
                $title = $arguments['otherWidgetTitle'] ?? 'другой виджет';

                return "Сейчас на сайте показывается «{$title}». Заменить?";
            })
            ->modalSubmitActionLabel('Заменить')
            ->color('warning')
            ->action(function (): void {
                $this->data['show_on_site'] = true;
            });
    }
}
