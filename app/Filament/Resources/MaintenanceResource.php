<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaintenanceResource\Pages;
use App\Filament\Resources\MaintenanceResource\RelationManagers;
use App\Models\Maintenance;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rule;

class MaintenanceResource extends Resource
{
    use Translatable;

    protected static ?string $model = Maintenance::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    public static function getTranslatableLocales(): array
    {
        return ['ar', 'en'];
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('dashboard.maintenance');
    }

    public static function getNavigationLabel(): string
    {
        return __('dashboard.maintenance_services');
    }

    public static function getModelLabel(): string
    {
        return __('dashboard.maintenance');
    }

    public static function getPluralModelLabel(): string
    {
        return __('dashboard.maintenance_services');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name.ar')
                    ->label(__('dashboard.arabic_name'))
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('name.en')
                    ->label(__('dashboard.english_name'))
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('content.ar')
                    ->label(__('dashboard.arabic_content'))
                    ->columnSpanFull()
                    ->rows(5),

                Forms\Components\Textarea::make('content.en')
                    ->label(__('dashboard.english_content'))
                    ->columnSpanFull()
                    ->rows(5),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label(__('dashboard.price'))
                            ->numeric()
                            ->required()
                            ->prefix('SAR')
                            ->minValue(0)
                            ->rules([
                                fn (Forms\Get $get) => Rule::when(
                                    $get('final_price') && $get('price') <= $get('final_price'),
                                    ['gt:final_price']
                                ),
                            ])
                            ->helperText(__('Original price (must be higher than final price)')),

                        Forms\Components\TextInput::make('final_price')
                            ->label(__('final price'))
                            ->numeric()
                            ->required()
                            ->prefix('SAR')
                            ->minValue(0)
                            ->lt('price')
                            ->helperText(__('Discounted price (must be less than regular price)')),
                    ]),

                Forms\Components\Toggle::make('is_active')
                    ->label(__('dashboard.active'))
                    ->default(true)
                    ->onColor('success')
                    ->offColor('danger')
                    ->onIcon('heroicon-s-check')
                    ->offIcon('heroicon-s-x-mark')
                    ->inline(false),

                SpatieMediaLibraryFileUpload::make('maintenances')
                    ->label(__('dashboard.images'))
                    ->collection('maintenances')
                    ->multiple()
                    ->reorderable()
                    ->image()
                    ->imageEditor()
                    ->columnSpanFull(),
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

                Tables\Columns\TextColumn::make('price')
                    ->label(__('dashboard.price'))
                    ->money('SAR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('final_price')
                    ->label(__('final price'))
                    ->money('SAR')
                    ->sortable(),

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
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\OrdersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMaintenances::route('/'),
            'create' => Pages\CreateMaintenance::route('/create'),
            'edit' => Pages\EditMaintenance::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])->latest();
    }
}
