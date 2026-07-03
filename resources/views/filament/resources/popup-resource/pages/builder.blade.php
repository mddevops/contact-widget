@php
    $submitMethod = $this instanceof \Filament\Resources\Pages\EditRecord ? 'save' : 'create';
@endphp

<x-filament-panels::page
    @class([
        'fi-resource-create-record-page' => $submitMethod === 'create',
        'fi-resource-edit-record-page' => $submitMethod === 'save',
    ])
>
    <x-filament-panels::form
        id="form"
        :wire:key="$this->getId() . '.forms.' . $this->getFormStatePath()"
        wire:submit="{{ $submitMethod }}"
        style="overflow: visible;"
    >
        <div class="popup-builder-layout flex gap-6">
            <div class="popup-builder-layout__preview min-w-0" style="position: sticky; top: 100px;">
                @include('contact-widget::filament.popups.preview')
            </div>

            <div class="popup-builder-layout__settings min-w-0">
                <div class="sticky top-6 space-y-4">
                    {{ $this->form }}
                </div>
            </div>
        </div>
    </x-filament-panels::form>

    <x-filament-panels::page.unsaved-data-changes-alert />

    <style>
        .popup-builder-layout {
            align-items: flex-start;
            min-height: 32rem;
        }

        .popup-builder-layout__preview {
            flex: 0 0 70%;
            max-width: 70%;
        }

        .popup-builder-layout__settings {
            flex: 0 0 calc(30% - 1.5rem);
            max-width: calc(30% - 1.5rem);
        }

        @media (max-width: 1023px) {
            .popup-builder-layout {
                flex-direction: column;
            }

            .popup-builder-layout__preview,
            .popup-builder-layout__settings {
                flex: 1 1 auto;
                max-width: 100%;
                width: 100%;
            }
        }
    </style>
</x-filament-panels::page>
