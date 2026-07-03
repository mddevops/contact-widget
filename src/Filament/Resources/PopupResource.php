<?php

namespace SiteApps\ContactWidget\Filament\Resources;

use SiteApps\ContactWidget\Enums\PopupMobileImagePosition;
use SiteApps\ContactWidget\Enums\PopupDisplayMode;
use SiteApps\ContactWidget\Enums\PopupFrequency;
use SiteApps\ContactWidget\Enums\PopupTriggerType;
use SiteApps\ContactWidget\Enums\PopupImagePosition;
use SiteApps\ContactWidget\Enums\PopupListMarkerStyle;
use SiteApps\ContactWidget\Filament\Forms\Components\RangeSlider;
use SiteApps\ContactWidget\Filament\Forms\SocialIconSelect;
use SiteApps\ContactWidget\Filament\Resources\PopupResource\Pages;
use SiteApps\ContactWidget\Models\Popup;
use SiteApps\ContactWidget\Support\Popup\PopupDisplayRules;
use SiteApps\ContactWidget\Support\Popup\PopupSettings;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PopupResource extends Resource
{
    protected static ?string $model = Popup::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Попапы';

    protected static ?string $modelLabel = 'попап';

    protected static ?string $pluralModelLabel = 'попапы';

    protected static ?string $navigationGroup = 'Виджет связи';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('preview_device')
                    ->default('desktop')
                    ->dehydrated(false)
                    ->live(),
                Tabs::make('popup_settings')
                    ->tabs([
                        Tabs\Tab::make('Контент')
                            ->schema(static::contentTabSchema()),
                        Tabs\Tab::make('Фотография')
                            ->schema(static::imageTabSchema()),
                        Tabs\Tab::make('Условия показа')
                            ->schema(static::displayRulesTabSchema()),
                    ])
                    ->columnSpanFull()
                    ->contained(false),
            ]);
    }

    /**
     * @return list<Forms\Components\Component>
     */
    protected static function contentTabSchema(): array
    {
        return [
            Section::make('Основное')
                ->schema([
                    TextInput::make('name')
                        ->label('Название попапа')
                        ->required()
                        ->maxLength(255)
                        ->live(),
                    Hidden::make('is_active')
                        ->default(true),
                ])
                ->columns(1),
            Section::make('Контент')
                ->schema([
                    TextInput::make('title')
                        ->label('Заголовок')
                        ->required()
                        ->maxLength(255)
                        ->default('Остались вопросы?')
                        ->live(),
                    RangeSlider::make('settings.title_size')
                        ->label('Размер заголовка')
                        ->min(18)
                        ->max(72)
                        ->default(32)
                        ->live()
                        ->hidden(fn (Forms\Get $get): bool => static::isMobilePreview($get))
                        ->dehydratedWhenHidden(),
                    RangeSlider::make('settings.mobile_title_size')
                        ->label('Размер заголовка')
                        ->min(18)
                        ->max(72)
                        ->default(32)
                        ->live()
                        ->hidden(fn (Forms\Get $get): bool => ! static::isMobilePreview($get))
                        ->dehydratedWhenHidden(),
                    TextInput::make('subtitle')
                        ->label('Подзаголовок')
                        ->maxLength(255)
                        ->live()
                        ->default('Оставьте номер и мы перезвоним'),
                    RangeSlider::make('settings.subtitle_size')
                        ->label('Размер подзаголовка')
                        ->min(8)
                        ->max(24)
                        ->default(16)
                        ->live()
                        ->hidden(fn (Forms\Get $get): bool => static::isMobilePreview($get))
                        ->dehydratedWhenHidden(),
                    RangeSlider::make('settings.mobile_subtitle_size')
                        ->label('Размер подзаголовка')
                        ->min(8)
                        ->max(24)
                        ->default(16)
                        ->live()
                        ->hidden(fn (Forms\Get $get): bool => ! static::isMobilePreview($get))
                        ->dehydratedWhenHidden(),
                    Textarea::make('benefits')
                        ->label('Список преимуществ')
                        ->helperText('Каждая строка — отдельный пункт списка.')
                        ->rows(4)
                        ->live()
                        ->default('Трейд-ин 300 000 ₽
                        Кредит от 0,01 %
                        Зимняя резина в подарок'),
                    Select::make('settings.list_marker')
                        ->label('Стиль маркеров списка')
                        ->options(PopupListMarkerStyle::class)
                        ->default(PopupListMarkerStyle::Check)
                        ->live(),
                    ColorPicker::make('settings.list_marker_color')
                        ->label('Цвет маркеров списка')
                        ->default('#22c55e')
                        ->live(),
                    RangeSlider::make('settings.benefits_size')
                        ->label('Размер текста списка')
                        ->min(8)
                        ->max(24)
                        ->default(14)
                        ->live()
                        ->hidden(fn (Forms\Get $get): bool => static::isMobilePreview($get))
                        ->dehydratedWhenHidden(),
                    RangeSlider::make('settings.mobile_benefits_size')
                        ->label('Размер текста списка')
                        ->min(8)
                        ->max(24)
                        ->default(14)
                        ->live()
                        ->hidden(fn (Forms\Get $get): bool => ! static::isMobilePreview($get))
                        ->dehydratedWhenHidden(),
                    RangeSlider::make('settings.content_padding')
                        ->label('Отступ контента')
                        ->min(0)
                        ->max(80)
                        ->suffix(' px')
                        ->default(20)
                        ->live()
                        ->hidden(fn (Forms\Get $get): bool => static::isMobilePreview($get))
                        ->dehydratedWhenHidden(),
                    RangeSlider::make('settings.mobile_content_padding')
                        ->label('Отступ контента')
                        ->min(0)
                        ->max(80)
                        ->suffix(' px')
                        ->default(20)
                        ->live()
                        ->hidden(fn (Forms\Get $get): bool => ! static::isMobilePreview($get))
                        ->dehydratedWhenHidden(),
                    ColorPicker::make('settings.content_background')
                        ->label('Цвет фона контента')
                        ->default('#ffffff')
                        ->live(),
                    RangeSlider::make('settings.content_width')
                        ->label('Ширина контента')
                        ->helperText('Ширина блока с текстом и формой в пикселях.')
                        ->min(280)
                        ->max(720)
                        ->suffix(' px')
                        ->default(480)
                        ->live()
                        ->hidden(fn (Forms\Get $get): bool => static::isMobilePreview($get))
                        ->dehydratedWhenHidden(),
                    RangeSlider::make('settings.mobile_content_width')
                        ->label('Ширина контента')
                        ->helperText('Ширина блока с текстом и формой в пикселях.')
                        ->min(260)
                        ->max(480)
                        ->suffix(' px')
                        ->default(360)
                        ->live()
                        ->hidden(fn (Forms\Get $get): bool => ! static::isMobilePreview($get))
                        ->dehydratedWhenHidden(),
                    RangeSlider::make('settings.border_radius')
                        ->label('Общее скругление')
                        ->min(0)
                        ->max(50)
                        ->default(16)
                        ->live(),
                ])
                ->columns(1),
            Section::make('Кнопка')
                ->schema([
                    TextInput::make('button_text')
                        ->label('Текст кнопки')
                        ->default('Отправить заявку')
                        ->required()
                        ->maxLength(100)
                        ->live(),
                    SocialIconSelect::make('settings.button_icon_id')
                        ->label('Иконка')
                        ->live(),
                    RangeSlider::make('settings.button_icon_size')
                        ->label('Размер иконки')
                        ->min(12)
                        ->max(48)
                        ->suffix(' px')
                        ->default(18)
                        ->live(),
                    ColorPicker::make('settings.button_icon_color')
                        ->label('Цвет иконки')
                        ->default('#ffffff')
                        ->live(),
                    ColorPicker::make('settings.button_color')
                        ->label('Цвет кнопки')
                        ->default('#22c55e')
                        ->live(),
                    ColorPicker::make('settings.button_text_color')
                        ->label('Цвет текста кнопки')
                        ->default('#ffffff')
                        ->live(),
                ])
                ->columns(1),
        ];
    }

    protected static function isMobilePreview(Forms\Get $get): bool
    {
        return ($get('preview_device') ?? 'desktop') === 'mobile';
    }

    /**
     * @return list<Forms\Components\Component>
     */
    protected static function imageTabSchema(): array
    {
        return [
            Section::make()
                ->heading(fn (Forms\Get $get): string => static::isMobilePreview($get) ? 'Мобильная версия' : 'Десктоп')
                ->schema([
                    FileUpload::make('image')
                        ->label('Загрузка изображения')
                        ->image()
                        ->directory('popups')
                        ->openable()
                        ->imageEditor()
                        ->live()
                        ->columnSpanFull(),
                    Toggle::make('settings.desktop_hide_image')
                        ->label('Скрыть фотографию')
                        ->default(false)
                        ->inline(false)
                        ->hidden(fn (Forms\Get $get): bool => static::isMobilePreview($get))
                        ->dehydratedWhenHidden()
                        ->live(),
                    Select::make('settings.image_position')
                        ->label('Расположение изображения')
                        ->options(PopupImagePosition::class)
                        ->default(PopupImagePosition::Left)
                        ->hidden(fn (Forms\Get $get): bool => static::isMobilePreview($get) || (bool) $get('settings.desktop_hide_image'))
                        ->dehydratedWhenHidden()
                        ->live(),
                    RangeSlider::make('settings.image_width')
                        ->label('Ширина изображения')
                        ->helperText('Ширина блока с фото в пикселях.')
                        ->min(200)
                        ->max(720)
                        ->suffix(' px')
                        ->default(480)
                        ->hidden(fn (Forms\Get $get): bool => static::isMobilePreview($get) || (bool) $get('settings.desktop_hide_image'))
                        ->dehydratedWhenHidden()
                        ->live(),
                    RangeSlider::make('settings.image_scale')
                        ->label('Масштаб изображения')
                        ->min(50)
                        ->max(500)
                        ->suffix('%')
                        ->default(100)
                        ->hidden(fn (Forms\Get $get): bool => static::isMobilePreview($get) || (bool) $get('settings.desktop_hide_image'))
                        ->dehydratedWhenHidden()
                        ->live(),
                    Select::make('settings.image_x')
                        ->label('Горизонтальное позиционирование')
                        ->options([
                            'left' => 'Слева',
                            'center' => 'По центру',
                            'right' => 'Справа',
                        ])
                        ->default('center')
                        ->hidden(fn (Forms\Get $get): bool => static::isMobilePreview($get) || (bool) $get('settings.desktop_hide_image'))
                        ->dehydratedWhenHidden()
                        ->live(),
                    Select::make('settings.image_y')
                        ->label('Вертикальное позиционирование')
                        ->options([
                            'top' => 'Сверху',
                            'center' => 'По центру',
                            'bottom' => 'Снизу',
                        ])
                        ->default('center')
                        ->hidden(fn (Forms\Get $get): bool => static::isMobilePreview($get) || (bool) $get('settings.desktop_hide_image'))
                        ->dehydratedWhenHidden()
                        ->live(),
                    Toggle::make('settings.mobile_hide_image')
                        ->label('Скрыть фотографию')
                        ->default(false)
                        ->inline(false)
                        ->hidden(fn (Forms\Get $get): bool => ! static::isMobilePreview($get))
                        ->dehydratedWhenHidden()
                        ->live(),
                    Select::make('settings.mobile_image_position')
                        ->label('Расположение фотографии')
                        ->options(PopupMobileImagePosition::class)
                        ->default(PopupMobileImagePosition::Top)
                        ->hidden(fn (Forms\Get $get): bool => ! static::isMobilePreview($get) || (bool) $get('settings.mobile_hide_image'))
                        ->dehydratedWhenHidden()
                        ->live(),
                    RangeSlider::make('settings.mobile_image_width_px')
                        ->label('Ширина изображения')
                        ->helperText('Ширина блока с фото в пикселях.')
                        ->min(260)
                        ->max(480)
                        ->suffix(' px')
                        ->default(360)
                        ->hidden(fn (Forms\Get $get): bool => ! static::isMobilePreview($get) || (bool) $get('settings.mobile_hide_image'))
                        ->dehydratedWhenHidden()
                        ->live(),
                    RangeSlider::make('settings.mobile_image_scale')
                        ->label('Масштаб изображения')
                        ->min(50)
                        ->max(500)
                        ->suffix('%')
                        ->default(100)
                        ->hidden(fn (Forms\Get $get): bool => ! static::isMobilePreview($get) || (bool) $get('settings.mobile_hide_image'))
                        ->dehydratedWhenHidden()
                        ->live(),
                    Select::make('settings.mobile_image_x')
                        ->label('Горизонтальное позиционирование')
                        ->options([
                            'left' => 'Слева',
                            'center' => 'По центру',
                            'right' => 'Справа',
                        ])
                        ->default('center')
                        ->hidden(fn (Forms\Get $get): bool => ! static::isMobilePreview($get) || (bool) $get('settings.mobile_hide_image'))
                        ->dehydratedWhenHidden()
                        ->live(),
                    Select::make('settings.mobile_image_y')
                        ->label('Вертикальное позиционирование')
                        ->options([
                            'top' => 'Сверху',
                            'center' => 'По центру',
                            'bottom' => 'Снизу',
                        ])
                        ->default('center')
                        ->hidden(fn (Forms\Get $get): bool => ! static::isMobilePreview($get) || (bool) $get('settings.mobile_hide_image'))
                        ->dehydratedWhenHidden()
                        ->live(),
                    RangeSlider::make('settings.mobile_image_height_px')
                        ->label('Высота блока с фото')
                        ->helperText('Высота изображения на мобильной версии.')
                        ->min(50)
                        ->max(500)
                        ->suffix(' px')
                        ->default(300)
                        ->hidden(fn (Forms\Get $get): bool => ! static::isMobilePreview($get) || (bool) $get('settings.mobile_hide_image'))
                        ->dehydratedWhenHidden()
                        ->live(),
                ])
                ->columns(1),
        ];
    }

    /**
     * @return list<Forms\Components\Component>
     */
    protected static function displayRulesTabSchema(): array
    {
        return [
            Section::make('Условия показа')
                ->schema([
                    Select::make('display_rules.mode')
                        ->label('Тип отображения')
                        ->options(PopupDisplayMode::class)
                        ->default(PopupDisplayMode::AllPages)
                        ->live(),
                    TagsInput::make('display_rules.url_paths')
                        ->label('URL страниц')
                        ->placeholder('contact')
                        ->helperText('Путь без домена: contact, catalog/kia. Для главной оставьте пустым или укажите /')
                        ->hidden(fn (Forms\Get $get): bool => $get('display_rules.mode') !== PopupDisplayMode::SelectedPages->value)
                        ->live(),
                    Toggle::make('display_rules.include_subpages')
                        ->label('Также во внутренних страницах')
                        ->helperText('Показывать попап на подстраницах выбранных разделов.')
                        ->default(false)
                        ->inline(false)
                        ->hidden(fn (Forms\Get $get): bool => $get('display_rules.mode') !== PopupDisplayMode::SelectedPages->value)
                        ->live(),
                    Select::make('display_rules.trigger')
                        ->label('Триггер показа')
                        ->options(PopupTriggerType::class)
                        ->default(PopupTriggerType::Delay)
                        ->hidden(fn (Forms\Get $get): bool => in_array($get('display_rules.mode'), [
                            PopupDisplayMode::ExitIntent->value,
                            PopupDisplayMode::ManualOnly->value,
                        ], true))
                        ->live(),
                    RangeSlider::make('display_rules.delay')
                        ->label('Задержка появления')
                        ->min(0)
                        ->max(300)
                        ->suffix(' сек')
                        ->default(5)
                        ->hidden(fn (Forms\Get $get): bool => in_array($get('display_rules.mode'), [
                            PopupDisplayMode::ExitIntent->value,
                            PopupDisplayMode::ManualOnly->value,
                        ], true)
                            || $get('display_rules.trigger') === PopupTriggerType::Scroll->value)
                        ->live(),
                    RangeSlider::make('display_rules.scroll_percent')
                        ->label('Прокрутка страницы')
                        ->min(1)
                        ->max(100)
                        ->suffix(' %')
                        ->default(50)
                        ->helperText('Показать попап после прокрутки на указанный процент высоты страницы.')
                        ->hidden(fn (Forms\Get $get): bool => in_array($get('display_rules.mode'), [
                            PopupDisplayMode::ExitIntent->value,
                            PopupDisplayMode::ManualOnly->value,
                        ], true)
                            || $get('display_rules.trigger') !== PopupTriggerType::Scroll->value)
                        ->live(),
                    Select::make('display_rules.frequency')
                        ->label('Частота показа')
                        ->options(PopupFrequency::class)
                        ->default(PopupFrequency::Visit)
                        ->hidden(fn (Forms\Get $get): bool => $get('display_rules.mode') === PopupDisplayMode::ManualOnly->value)
                        ->live(),
                    RangeSlider::make('display_rules.session_limit')
                        ->label('Лимит показов за сессию')
                        ->min(1)
                        ->max(10)
                        ->suffix('')
                        ->default(1)
                        ->helperText('Сколько раз можно показать попап за одну сессию браузера.')
                        ->hidden(fn (Forms\Get $get): bool => $get('display_rules.mode') === PopupDisplayMode::ManualOnly->value),
                ])
                ->columns(1),
            Section::make('Расписание')
                ->hidden(fn (Forms\Get $get): bool => $get('display_rules.mode') === PopupDisplayMode::ManualOnly->value)
                ->schema([
                    Toggle::make('display_rules.schedule.enabled')
                        ->label('Ограничить по времени')
                        ->default(false)
                        ->inline(false)
                        ->live(),
                    CheckboxList::make('display_rules.schedule.days')
                        ->label('Дни недели')
                        ->options([
                            1 => 'Понедельник',
                            2 => 'Вторник',
                            3 => 'Среда',
                            4 => 'Четверг',
                            5 => 'Пятница',
                            6 => 'Суббота',
                            7 => 'Воскресенье',
                        ])
                        ->columns(2)
                        ->default([1, 2, 3, 4, 5, 6, 7])
                        ->hidden(fn (Forms\Get $get): bool => ! $get('display_rules.schedule.enabled'))
                        ->live(),
                    TimePicker::make('display_rules.schedule.from')
                        ->label('С')
                        ->seconds(false)
                        ->default('09:00')
                        ->hidden(fn (Forms\Get $get): bool => ! $get('display_rules.schedule.enabled'))
                        ->live(),
                    TimePicker::make('display_rules.schedule.to')
                        ->label('До')
                        ->seconds(false)
                        ->default('18:00')
                        ->hidden(fn (Forms\Get $get): bool => ! $get('display_rules.schedule.enabled'))
                        ->live(),
                ])
                ->columns(1),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Статус')
                    ->sortable()
                    ->onColor('success')
                    ->offColor('warning'),
                Tables\Columns\TextColumn::make('display_rules')
                    ->label('Тип отображения')
                    ->formatStateUsing(fn ($state, Popup $record): string => PopupDisplayRules::displayModeLabel($record->display_rules)),
                Tables\Columns\TextColumn::make('display_rules_summary')
                    ->label('Условия показа')
                    ->state(fn (Popup $record): string => PopupDisplayRules::conditionsSummary($record->display_rules)),
                Tables\Columns\TextColumn::make('show_delay')
                    ->label('Задержка')
                    ->state(fn (Popup $record): string => PopupDisplayRules::isExitIntent($record->display_rules)
                        || PopupDisplayRules::isManualOnly($record->display_rules)
                        ? '—'
                        : (($record->resolvedDisplayRules()['trigger'] ?? 'delay') === PopupTriggerType::Scroll->value
                            ? (($record->resolvedDisplayRules()['scroll_percent'] ?? 50) . '%')
                            : (($record->resolvedDisplayRules()['delay'] ?? 0) . ' сек'))),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата создания')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Дата обновления')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Активность')
                    ->trueLabel('Активные')
                    ->falseLabel('Неактивные')
                    ->placeholder('Все'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label(false),
                Tables\Actions\ReplicateAction::make()
                    ->label('Дублировать')
                    ->excludeAttributes(['created_at', 'updated_at'])
                    ->beforeReplicaSaved(function (Popup $replica): void {
                        $replica->name = $replica->name . ' (копия)';
                        $replica->is_active = false;
                    }),
                Tables\Actions\DeleteAction::make()->label(false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Включить')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn ($records) => $records->each->update(['is_active' => true])),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Выключить')
                        ->icon('heroicon-o-x-circle')
                        ->action(fn ($records) => $records->each->update(['is_active' => false])),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPopups::route('/'),
            'create' => Pages\CreatePopup::route('/create'),
            'edit' => Pages\EditPopup::route('/{record}/edit'),
        ];
    }
}
