<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Filament\Resources\SettingResource\RelationManagers;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('key')
                ->required()
                ->reactive(), // عشان تتفاعل مع أي تغير لو حبيت تضيف حاجة حسب key

            Forms\Components\Select::make('type')
                ->options([
                    'text' => 'Text',
                    'textarea' => 'Textarea',
                    'image' => 'Image',
                    'number' => 'Number',
                    'boolean' => 'Boolean',
                ])
                ->required()
                ->reactive(),

            Forms\Components\Group::make([
                Forms\Components\TextInput::make('value')
                    ->label('Value (Text)')
                    ->visible(fn ($get) => $get('type') === 'text'),

                Forms\Components\Textarea::make('value')
                    ->label('Value (Textarea)')
                    ->visible(fn ($get) => $get('type') === 'textarea'),

                Forms\Components\TextInput::make('value')
                    ->label('Value (Number)')
                    ->numeric()
                    ->visible(fn ($get) => $get('type') === 'number'),

                Forms\Components\Toggle::make('value')
                    ->label('Value (Boolean)')
                    ->visible(fn ($get) => $get('type') === 'boolean')
                    ->default(false),

                Forms\Components\FileUpload::make('value')
                    ->label('Value (Image)')
                    ->directory('settings')
                    ->image()
                    ->visible(fn ($get) => $get('type') === 'image'),
            ])
                ->columnSpanFull()
                ->reactive(),
        ]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
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
