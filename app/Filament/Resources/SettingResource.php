<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Filament\Resources\SettingResource\RelationManagers;
use App\Models\Setting;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Set;
class SettingResource extends Resource
{
    use Translatable;
    protected static ?string $model = Setting::class;
    public static function getNavigationSort(): ?int
    {
        return 9;
    }
    public static function getNavigationGroup( ): ?string
    {
        return __('Management' );

    }
    public static function getNavigationLabel(): string
    {
        return __('dashboard.settings');
    }
    public static function getModelLabel(): string
    {
        return __('dashboard.setting');
    }
    public static function getPluralModelLabel(): string
    {
        return __('dashboard.settings');
    }
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function getTranslatableLocales(): array
    {
        return ['ar','en' ];
    }
    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\TextInput::make(__('name.en'))
//                ->label('Name (English)')
                ->label(__('dashboard.arabic_name'))
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('name.ar')
                ->label(__('dashboard.english_name'))
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('key')
                ->label(__('key'))
                ->required()
                ->disabled(fn($record) => $record !== null),

            Forms\Components\Select::make('type')
                ->label(__('type'))
                ->options([
                    'text' => __('text'),
                    'textarea' => __('textarea'),
                    'image' => __('image'),
                    'number' => __('number'),
                ])
                ->required()
                ->reactive()
                ->disabled(fn($record) => $record !== null)
                ->afterStateUpdated(function (Set $set) {
                    $set('value', null);
                }),


            Forms\Components\Group::make([
                // Text Field
                Forms\Components\TextInput::make('value')
                    ->label(__('value'))
                    ->visible(fn($get) => $get('type') === 'text')
                    ->required(fn($get) => $get('type') === 'text'),

                // Textarea Field
                Forms\Components\Textarea::make('value')
                    ->label('Value (Textarea)')
                    ->visible(fn($get) => $get('type') === 'textarea')
                    ->required(fn($get) => $get('type') === 'textarea'),

                // Number Field
                Forms\Components\TextInput::make('value')
                    ->label('Value (Number)')
                    ->numeric()
                    ->visible(fn($get) => $get('type') === 'number')
                    ->required(fn($get) => $get('type') === 'number'),

                // Boolean Field
                Forms\Components\Toggle::make('value')
                    ->label('Value (Boolean)')
                    ->visible(fn($get) => $get('type') === 'boolean')
                    ,

                // Image Field
                Forms\Components\FileUpload::make('image')
                    ->label(trans('image'))
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
                    ->label(__('name'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('value')
                    ->label(__('value'))
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
        return [
            //
        ];
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
