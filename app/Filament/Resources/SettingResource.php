<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Set;

class SettingResource extends Resource
{
    use Translatable;

    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function getNavigationLabel(): string
    {
        return __('app.Settings');
    }

    protected static ?string $modelLabel = 'Setting'; // ترجم هنا
    protected static ?string $pluralModelLabel = 'Settings'; // ترجم هنا

    public static function getTranslatableLocales(): array
    {
        return ['ar', 'en'];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\TextInput::make('name.en')
                ->label(__('app.nmr'))
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('name.ar')
                ->label(__('app.name_ar'))
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('key')
                ->label(__('app.key'))
                ->required()
                ->disabled(fn($record) => $record !== null),

            Forms\Components\Select::make('type')
                ->label(__('app.type'))
                ->options([
                    'text' => __('app.text'),
                    'textarea' => __('app.textarea'),
                    'image' => __('app.image'),
                    'number' => __('app.number'),
                    'boolean' => __('app.boolean'),
                ])
                ->required()
                ->reactive()
                ->disabled(fn($record) => $record !== null)
                ->afterStateUpdated(function (Set $set) {
                    $set('value', null);
                }),

            Forms\Components\Group::make([
                Forms\Components\TextInput::make('value')
                    ->label(__('app.value_text'))
                    ->visible(fn($get) => $get('type') === 'text')
                    ->required(fn($get) => $get('type') === 'text'),

                Forms\Components\Textarea::make('value')
                    ->label(__('app.value_textarea'))
                    ->visible(fn($get) => $get('type') === 'textarea')
                    ->required(fn($get) => $get('type') === 'textarea'),

                Forms\Components\TextInput::make('value')
                    ->label(__('app.value_number'))
                    ->numeric()
                    ->visible(fn($get) => $get('type') === 'number')
                    ->required(fn($get) => $get('type') === 'number'),

                Forms\Components\Toggle::make('value')
                    ->label(__('app.value_boolean'))
                    ->visible(fn($get) => $get('type') === 'boolean'),

                Forms\Components\FileUpload::make('image')
                    ->label(__('app.value_image'))
                    ->visible(fn($get) => $get('type') === 'image')
                    ->required(fn($get) => $get('type') === 'image')
                    ->acceptedFileTypes(['image/*'])
                    ->directory('settings-temp')
                    ->preserveFilenames()
                    ->getUploadedFileNameForStorageUsing(fn($file) => $file->getClientOriginalName())
                    ->dehydrated(false)
                    ->multiple(false),
            ])
                ->columnSpanFull()
                ->reactive(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('value')
                    ->label(__('app.value'))
                    ->getStateUsing(function (Setting $record) {
                        if ($record->type === 'image') {
                            return $record->getFirstMediaUrl('settings');
                        }
                        return $record->value;
                    })
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->type === 'image') {
                            return '<img src="' . $state . '" width="100">';
                        }
                        return $state;
                    })
                    ->html(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
