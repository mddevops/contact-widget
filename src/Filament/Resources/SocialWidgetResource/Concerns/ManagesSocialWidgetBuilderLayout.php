<?php

namespace SiteApps\ContactWidget\Filament\Resources\SocialWidgetResource\Concerns;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\View\View;

trait ManagesSocialWidgetBuilderLayout
{
    public function getHeader(): ?View
    {
        return view('contact-widget::filament.resources.social-widget-resource.pages.builder-header', [
            'heading' => $this->getTitle(),
            'breadcrumbs' => filament()->hasBreadcrumbs() ? $this->getBreadcrumbs() : [],
            'actions' => $this->getCachedHeaderActions(),
        ]);
    }

    /**
     * @return array<Action | DeleteAction>
     */
    protected function getHeaderActions(): array
    {
        $actions = [
            $this->getCancelFormAction()->label('Отмена'),
        ];

        if ($this instanceof EditRecord) {
            $actions[] = DeleteAction::make();
            $actions[] = $this->getSaveFormAction()
                ->label('Сохранить')
                ->formId('form');
        } elseif ($this instanceof CreateRecord) {
            $actions[] = $this->getCreateFormAction()
                ->label('Сохранить')
                ->formId('form');
        }

        return $actions;
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
