<?php

namespace SiteApps\ContactWidget\Filament\Resources;

use SiteApps\ContactWidget\Filament\Resources\SocialIconResource\Pages;
use SiteApps\ContactWidget\Models\SocialIcon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class SocialIconResource extends Resource
{
    protected static ?string $model = SocialIcon::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationLabel = 'Иконки виджета';

    protected static ?string $modelLabel = 'иконку';

    protected static ?string $pluralModelLabel = 'иконки';

    protected static ?string $navigationGroup = 'Виджет связи';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Название')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Textarea::make('svg')
                            ->label('SVG')
                            ->required()
                            ->rows(10)
                            ->live()
                            ->helperText('Поддерживаются заливные иконки и контурные (Tabler, Heroicons). При сохранении SVG нормализуется под цвет кнопки.'),
                        Forms\Components\Placeholder::make('svg_preview')
                            ->label('Предпросмотр')
                            ->content(fn (Forms\Get $get): HtmlString => new HtmlString(
                                '<div class="flex h-16 w-16 items-center justify-center rounded-lg border border-gray-200 bg-gray-50 text-violet-600 dark:border-gray-700 dark:bg-gray-900">'
                                . ($get('svg') ?: '<span class="text-xs text-gray-400">SVG</span>')
                                . '</div>'
                            )),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ViewColumn::make('svg')
                    ->label('Preview')
                    ->view('contact-widget::filament.social.columns.icon-preview'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSocialIcons::route('/'),
            'create' => Pages\CreateSocialIcon::route('/create'),
            'edit' => Pages\EditSocialIcon::route('/{record}/edit'),
        ];
    }
}
