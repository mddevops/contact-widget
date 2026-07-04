<?php

namespace SiteApps\ContactWidget\Filament\Resources;

use SiteApps\ContactWidget\Enums\SocialWidgetAnimation;
use SiteApps\ContactWidget\Enums\SocialWidgetPosition;
use SiteApps\ContactWidget\Filament\Forms\Components\RangeSlider;
use SiteApps\ContactWidget\Filament\Forms\SocialIconSelect;
use SiteApps\ContactWidget\Filament\Resources\SocialWidgetResource\Pages;
use SiteApps\ContactWidget\Filament\Resources\SocialWidgetResource\RelationManagers\ButtonsRelationManager;
use SiteApps\ContactWidget\Models\SocialIcon;
use SiteApps\ContactWidget\Models\SocialWidget;
use SiteApps\ContactWidget\Support\Social\SocialWidgetMobileSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SocialWidgetResource extends Resource
{
    protected static ?string $model = SocialWidget::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Виджет связи';

    protected static ?string $modelLabel = 'виджет';

    protected static ?string $pluralModelLabel = 'виджеты';

    protected static ?string $navigationGroup = 'Виджет связи';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('social_widget_tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Настройки')
                            ->schema(static::settingsTabSchema()),
                        Forms\Components\Tabs\Tab::make('Виджет связи')
                            ->schema([
                                Forms\Components\Repeater::make('buttons')
                                    ->relationship('buttons')
                                    ->label('Кнопки')
                                    ->orderColumn('sort')
                                    ->reorderable()
                                    ->reorderableWithDragAndDrop()
                                    ->collapsible()
                                    ->defaultItems(1)
                                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Кнопка')
                                    ->schema(ButtonsRelationManager::buttonFields())
                                    ->mutateRelationshipDataBeforeCreateUsing(fn (array $data): array => array_merge(
                                        ButtonsRelationManager::defaultItemState(),
                                        $data,
                                    ))
                                    ->live()
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->contained(false),
            ]);
    }

    /**
     * @return list<Forms\Components\Component>
     */
    protected static function settingsTabSchema(): array
    {
        return [
            Forms\Components\Hidden::make('preview_device')
                ->default('desktop')
                ->dehydrated(false)
                ->live(),
            Forms\Components\Section::make()
                ->schema([
                    static::showOnSiteToggle(),
                    Forms\Components\Hidden::make('open_direction')
                        ->default('up')
                        ->dehydrated(true),
                    Forms\Components\TextInput::make('title')
                        ->label('Название')
                        ->required()
                        ->maxLength(255)
                        ->live(),
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Toggle::make('desktop_only')
                                ->label('Только десктоп')
                                ->inline(false)
                                ->live(),
                            Forms\Components\Toggle::make('mobile_only')
                                ->label('Только мобильные')
                                ->inline(false)
                                ->live(),
                        ]),
                ])
                ->columns(1),
            Forms\Components\Section::make('Главная кнопка')
                ->schema([
                    ...static::sharedMainButtonFields(),
                    ...static::desktopMainButtonFields(),
                    ...static::mobileMainButtonFields(),
                ])
                ->columns(1),
            Forms\Components\Section::make('Виджет кнопки')
                ->schema([
                    ...static::desktopWidgetButtonsFields(),
                    ...static::mobileWidgetButtonsFields(),
                ])
                ->columns(1),
        ];
    }

    /**
     * @return list<Forms\Components\Component>
     */
    protected static function desktopMainButtonFields(): array
    {
        return [
            Forms\Components\Select::make('position')
                ->label('Позиция')
                ->options(static::desktopPositionOptions())
                ->default(SocialWidgetPosition::Right)
                ->required()
                ->live()
                ->hidden(fn (Get $get): bool => static::isMobilePreview($get)),
            RangeSlider::make('offset_bottom')
                ->label('Отступ снизу')
                ->min(0)
                ->max(200)
                ->suffix(' px')
                ->default(40)
                ->live()
                ->hidden(fn (Get $get): bool => static::isMobilePreview($get)),
            RangeSlider::make('offset_side')
                ->label(fn (Get $get): string => static::sideOffsetLabel($get('position') ?? SocialWidgetPosition::Right->value))
                ->helperText(fn (Get $get): string => static::sideOffsetHelper($get('position') ?? SocialWidgetPosition::Right->value))
                ->min(0)
                ->max(200)
                ->suffix(' px')
                ->default(40)
                ->live()
                ->hidden(fn (Get $get): bool => static::isMobilePreview($get)),
            ...static::withDeviceVisibility(static::mainButtonStyleFields(prefix: ''), mobile: false),
        ];
    }

    /**
     * @return list<Forms\Components\Component>
     */
    protected static function mobileMainButtonFields(): array
    {
        return [
            Forms\Components\Select::make('mobile_settings.position')
                ->label('Позиция')
                ->options(SocialWidgetPosition::class)
                ->default(SocialWidgetPosition::Right)
                ->required()
                ->live()
                ->hidden(fn (Get $get): bool => ! static::isMobilePreview($get)),
            RangeSlider::make('mobile_settings.offset_bottom')
                ->label('Отступ снизу')
                ->min(0)
                ->max(200)
                ->suffix(' px')
                ->default(SocialWidgetMobileSettings::defaults()['offset_bottom'])
                ->live()
                ->hidden(fn (Get $get): bool => ! static::isMobilePreview($get)),
            RangeSlider::make('mobile_settings.offset_side')
                ->label(fn (Get $get): string => static::sideOffsetLabel($get('mobile_settings.position') ?? SocialWidgetPosition::Right->value))
                ->helperText(fn (Get $get): string => static::sideOffsetHelper($get('mobile_settings.position') ?? SocialWidgetPosition::Right->value))
                ->min(0)
                ->max(200)
                ->suffix(' px')
                ->default(SocialWidgetMobileSettings::defaults()['offset_side'])
                ->hidden(fn (Get $get): bool => ! static::isMobilePreview($get)
                    || ($get('mobile_settings.position') ?? SocialWidgetPosition::Right->value) === SocialWidgetPosition::Center->value)
                ->live(),
            ...static::withDeviceVisibility(static::mainButtonStyleFields(prefix: 'mobile_settings.'), mobile: true),
        ];
    }

    /**
     * @return list<Forms\Components\Component>
     */
    protected static function desktopWidgetButtonsFields(): array
    {
        return [
            static::buttonDisplayRadio(
                field: '_button_display',
                showLabelsPath: 'show_labels',
                tooltipPath: 'tooltip_enabled',
                recordResolver: fn (?SocialWidget $record): string => $record?->show_labels ? 'labels' : 'tooltip',
            )->hidden(fn (Get $get): bool => static::isMobilePreview($get)),
            Forms\Components\Hidden::make('tooltip_enabled')
                ->default(false)
                ->dehydrated(),
            Forms\Components\Hidden::make('show_labels')
                ->default(true)
                ->dehydrated(),
            ...static::withDeviceVisibility(static::widgetButtonsStyleFields(
                itemIconSizePath: 'item_icon_size',
                itemFontSizePath: 'item_font_size',
                panelBackgroundPath: 'popup_background',
                panelOpacityPath: 'panel_background_opacity',
                panelBorderRadiusPath: 'popup_border_radius',
            ), mobile: false),
        ];
    }

    /**
     * @return list<Forms\Components\Component>
     */
    protected static function mobileWidgetButtonsFields(): array
    {
        return [
            static::buttonDisplayRadio(
                field: '_mobile_button_display',
                showLabelsPath: 'mobile_settings.show_labels',
                tooltipPath: 'mobile_settings.tooltip_enabled',
                recordResolver: fn (?SocialWidget $record): string => ($record?->mergedMobileSettings()['show_labels'] ?? false) ? 'labels' : 'tooltip',
            )->hidden(fn (Get $get): bool => ! static::isMobilePreview($get)),
            Forms\Components\Hidden::make('mobile_settings.tooltip_enabled')
                ->default(false)
                ->dehydrated(),
            Forms\Components\Hidden::make('mobile_settings.show_labels')
                ->default(true)
                ->dehydrated(),
            ...static::withDeviceVisibility(static::widgetButtonsStyleFields(
                itemIconSizePath: 'mobile_settings.item_icon_size',
                itemFontSizePath: 'mobile_settings.item_font_size',
                panelBackgroundPath: 'mobile_settings.panel_background',
                panelOpacityPath: 'mobile_settings.panel_background_opacity',
                panelBorderRadiusPath: 'mobile_settings.panel_border_radius',
            ), mobile: true),
        ];
    }

    protected static function isMobilePreview(Get $get): bool
    {
        return ($get('preview_device') ?? 'desktop') === 'mobile';
    }

    /**
     * @param  list<Forms\Components\Component>  $fields
     * @return list<Forms\Components\Component>
     */
    protected static function withDeviceVisibility(array $fields, bool $mobile): array
    {
        return array_map(function (Forms\Components\Component $field) use ($mobile): Forms\Components\Component {
            return $field->hidden(fn (Get $get): bool => static::isMobilePreview($get) !== $mobile);
        }, $fields);
    }

    /**
     * @return list<Forms\Components\Component>
     */
    protected static function widgetButtonsStyleFields(
        string $itemIconSizePath,
        string $itemFontSizePath,
        string $panelBackgroundPath,
        string $panelOpacityPath,
        string $panelBorderRadiusPath,
    ): array {
        return [
            RangeSlider::make($itemIconSizePath)
                ->label('Размер иконок')
                ->min(12)
                ->max(40)
                ->suffix(' px')
                ->default(18)
                ->live(),
            RangeSlider::make($itemFontSizePath)
                ->label('Размер шрифта')
                ->min(12)
                ->max(32)
                ->suffix(' px')
                ->default(14)
                ->live(),
            Forms\Components\ColorPicker::make($panelBackgroundPath)
                ->label('Цвет фона')
                ->default('#ffffff')
                ->live(),
            RangeSlider::make($panelOpacityPath)
                ->label('Прозрачность фона')
                ->min(0)
                ->max(100)
                ->suffix(' %')
                ->default(100)
                ->live(),
            RangeSlider::make($panelBorderRadiusPath)
                ->label('Скругление')
                ->min(0)
                ->max(40)
                ->suffix(' px')
                ->default(6)
                ->live(),
        ];
    }

    /**
     * @return list<Forms\Components\Component>
     */
    protected static function sharedMainButtonFields(): array
    {
        return [
            SocialIconSelect::make('main_icon_id')
                ->label('Иконка')
                ->required()
                ->default(fn (): ?int => SocialIcon::query()->where('slug', 'phone')->value('id'))
                ->live(),
            Forms\Components\Select::make('animation')
                ->label('Анимация')
                ->options(SocialWidgetAnimation::class)
                ->default(SocialWidgetAnimation::Pulse)
                ->required()
                ->live(),
        ];
    }

    /**
     * @return list<Forms\Components\Component>
     */
    protected static function mainButtonStyleFields(string $prefix): array
    {
        return [
            RangeSlider::make($prefix.'main_button_size')
                ->label('Размер кнопки')
                ->min(35)
                ->max(80)
                ->suffix(' px')
                ->default(48)
                ->live(),
            RangeSlider::make($prefix.'main_icon_size')
                ->label('Размер иконки')
                ->min(12)
                ->max(40)
                ->suffix(' px')
                ->default(30)
                ->live(),
            Forms\Components\ColorPicker::make($prefix.'main_button_color')
                ->label('Цвет кнопки')
                ->default('#8e36ff')
                ->live(),
            Forms\Components\Hidden::make($prefix.'main_button_text_color')
                ->default('#ffffff')
                ->dehydrated(),
        ];
    }

    protected static function buttonDisplayRadio(
        string $field,
        string $showLabelsPath,
        string $tooltipPath,
        callable $recordResolver,
    ): Forms\Components\Radio {
        return Forms\Components\Radio::make($field)
            ->label('Отображение кнопок')
            ->options([
                'tooltip' => 'Только иконки',
                'labels' => 'Иконка и подпись',
            ])
            ->descriptions([
                'tooltip' => 'Компактный вид, при наведении — подсказка',
                'labels' => 'Текст на кнопке, панель с фиксированной шириной',
            ])
            ->default('labels')
            ->inline(false)
            ->live()
            ->dehydrated(false)
            ->afterStateHydrated(function (Forms\Components\Radio $component, ?SocialWidget $record) use ($recordResolver): void {
                if ($record) {
                    $component->state($recordResolver($record));
                }
            })
            ->afterStateUpdated(function (?string $state, Set $set) use ($showLabelsPath, $tooltipPath): void {
                $set($showLabelsPath, $state === 'labels');
                $set($tooltipPath, $state !== 'labels');
            });
    }

    /**
     * @return array<string, string>
     */
    protected static function desktopPositionOptions(): array
    {
        return collect(SocialWidgetPosition::cases())
            ->reject(fn (SocialWidgetPosition $position): bool => $position === SocialWidgetPosition::Center)
            ->mapWithKeys(fn (SocialWidgetPosition $position): array => [$position->value => $position->getLabel()])
            ->all();
    }

    protected static function sideOffsetLabel(string $position): string
    {
        return match ($position) {
            SocialWidgetPosition::Left->value => 'Отступ слева',
            SocialWidgetPosition::Center->value => 'Отступ по горизонтали',
            default => 'Отступ справа',
        };
    }

    protected static function sideOffsetHelper(string $position): string
    {
        return match ($position) {
            SocialWidgetPosition::Left->value => '0 — вплотную к левому краю экрана.',
            SocialWidgetPosition::Center->value => 'Не используется при позиции по центру.',
            default => '0 — вплотную к правому краю экрана.',
        };
    }

    protected static function showOnSiteToggle(): Forms\Components\Toggle
    {
        return Forms\Components\Toggle::make('show_on_site')
            ->label('Показывать на сайте')
            ->helperText('На сайте одновременно может отображаться только один виджет.')
            ->default(false)
            ->inline(false)
            ->live()
            ->afterStateUpdated(function (bool $state, Set $set, $livewire): void {
                if (! $state) {
                    return;
                }

                $currentId = $livewire->record?->id;
                $other = SocialWidget::currentlyOnSite($currentId);

                if (! $other) {
                    return;
                }

                $set('show_on_site', false);

                $livewire->mountAction('confirmReplaceOnSite', [
                    'otherWidgetTitle' => $other->title,
                ]);
            });
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('show_on_site')
                    ->label('Показывать на сайте')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-minus'),
                Tables\Columns\TextColumn::make('buttons_count')
                    ->label('Кнопок')
                    ->counts('buttons'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Обновлён')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSocialWidgets::route('/'),
            'create' => Pages\CreateSocialWidget::route('/create'),
            'edit' => Pages\EditSocialWidget::route('/{record}/edit'),
        ];
    }
}
