<?php

namespace SiteApps\ContactWidget\Filament\Resources\SocialWidgetResource\RelationManagers;

use SiteApps\ContactWidget\Enums\SocialWidgetButtonOpenType;
use SiteApps\ContactWidget\Filament\Forms\SocialIconSelect;
use SiteApps\ContactWidget\Models\Popup;
use SiteApps\ContactWidget\Models\SocialIcon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ButtonsRelationManager extends RelationManager
{
    protected static string $relationship = 'buttons';

    protected static ?string $title = 'Кнопки';

    public function form(Form $form): Form
    {
        return $form->schema(static::buttonFields());
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Название')
                    ->searchable(),
                Tables\Columns\TextColumn::make('open_type')
                    ->label('Открытие')
                    ->badge(),
                Tables\Columns\IconColumn::make('enabled')
                    ->label('Активна')
                    ->boolean(),
                Tables\Columns\ViewColumn::make('icon.svg')
                    ->label('Иконка')
                    ->view('contact-widget::filament.social.columns.icon-preview'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Добавить кнопку'),
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

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function normalizeButtonData(array $data): array
    {
        $normalized = self::defaultItemState();

        foreach ($data as $key => $value) {
            if ($value !== null) {
                $normalized[$key] = $value;
            }
        }

        $normalized['enabled'] = true;

        $openType = $normalized['open_type'] ?? null;

        if ($openType instanceof SocialWidgetButtonOpenType) {
            $openType = $openType->value;
        }

        $normalized['open_type'] = filled($openType)
            ? (string) $openType
            : SocialWidgetButtonOpenType::Phone->value;

        return $normalized;
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultItemState(): array
    {
        return [
            'enabled' => true,
            'title' => 'Позвонить',
            'background_color' => '#8e36ff',
            'text_color' => '#ffffff',
            'open_type' => SocialWidgetButtonOpenType::Phone->value,
            'icon_id' => SocialIcon::query()->where('slug', 'phone')->value('id'),
            'sort' => 0,
        ];
    }

    /**
     * @return list<Forms\Components\Component>
     */
    public static function buttonFields(): array
    {
        return [
            Forms\Components\Hidden::make('enabled')
                ->default(true)
                ->dehydrated(),
            Forms\Components\TextInput::make('title')
                ->label('Название')
                ->required()
                ->maxLength(255)
                ->default('Позвонить')
                ->live(),
            SocialIconSelect::make('icon_id')
                ->label('Иконка')
                ->default(fn (): ?int => SocialIcon::query()->where('slug', 'phone')->value('id'))
                ->live(),
            Forms\Components\ColorPicker::make('background_color')
                ->label('Цвет кнопки')
                ->default('#8e36ff')
                ->required()
                ->live(),
            Forms\Components\ColorPicker::make('text_color')
                ->label('Цвет текста')
                ->default('#ffffff')
                ->required()
                ->live(),
            Forms\Components\Select::make('open_type')
                ->label('Тип открытия')
                ->options(SocialWidgetButtonOpenType::class)
                ->default(SocialWidgetButtonOpenType::Phone->value)
                ->required()
                ->live(),
            Forms\Components\TextInput::make('url')
                ->label('Ссылка')
                ->maxLength(500)
                ->visible(fn (Forms\Get $get): bool => $get('open_type') === 'url')
                ->live(),
            Forms\Components\TextInput::make('phone')
                ->label('Телефон')
                ->tel()
                ->maxLength(50)
                ->visible(fn (Forms\Get $get): bool => $get('open_type') === 'phone')
                ->live(),
            Forms\Components\Select::make('popup_id')
                ->label('Попап')
                ->options(fn (): array => Popup::query()
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->all())
                ->searchable()
                ->preload()
                ->visible(fn (Forms\Get $get): bool => $get('open_type') === 'popup')
                ->live(),
            Forms\Components\Hidden::make('sort')
                ->default(0),
        ];
    }
}
