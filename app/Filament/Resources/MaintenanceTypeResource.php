<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaintenanceTypeResource\Pages;
use App\Models\MaintenanceType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MaintenanceTypeResource extends Resource
{
    use Translatable;

    protected static ?string $model = MaintenanceType::class;

    public static function getNavigationLabel(): string
    {
        return __('dashboard.maintenance_types');
    }

    public static function getModelLabel(): string
    {
        return __('dashboard.maintenance_type');
    }

    public static function getPluralModelLabel(): string
    {
        return __('dashboard.maintenance_types');
    }

    protected static ?string $navigationIcon = 'heroicon-o-wrench';

    public static function getTranslatableLocales(): array
    {
        return ['ar', 'en'];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name.ar')
                ->label(__('dashboard.arabic_name'))
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('name.en')
                ->label(__('dashboard.english_name'))
                ->required()
                ->maxLength(255),

            Forms\Components\Toggle::make('is_active')
                ->label(__('dashboard.active'))
                ->default(true)
                ->onColor('success')
                ->offColor('danger')
                ->onIcon('heroicon-s-check')
                ->offIcon('heroicon-s-x-mark')
                ->inline(false)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('dashboard.name'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\BooleanColumn::make('is_active')
                    ->label(__('dashboard.active'))
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->label(__('dashboard.activation'))
                    ->options([
                        1 => __('dashboard.active'),
                        0 => __('dashboard.inactive'),
                    ]),

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
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMaintenanceTypes::route('/'),
            'create' => Pages\CreateMaintenanceType::route('/create'),
            'edit' => Pages\EditMaintenanceType::route('/{record}/edit'),
        ];
    }
}
