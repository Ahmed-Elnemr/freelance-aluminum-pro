<?php

namespace App\Filament\Resources;

use App\Enum\CategoryEnum;
use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Models\Service;
use App\Models\CategoryService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServiceResource extends Resource
{
    use Translatable;

    protected static ?string $model = Service::class;


    public static function getNavigationLabel(): string
    {
        return __('dashboard.services');
    }

    public static function getModelLabel(): string
    {
        return __('dashboard.service');
    }

    public static function getPluralModelLabel(): string
    {
        return __('dashboard.services');
    }

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    public static function getTranslatableLocales(): array
    {
        return ['ar', 'en'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\FileUpload::make('images')
                    ->label(__('dashboard.images'))
                    ->multiple()
                    ->image()
                    ->imageEditor()
                    ->directory('services')
                    ->reorderable()
                    ->appendFiles()
                    ->downloadable()
                    ->openable()
                    ->preserveFilenames()
                    ->columnSpanFull(),


                Forms\Components\Select::make('category_service_id')
                    ->label(__('dashboard.category_service'))
                    ->options(
                        CategoryService::active()->pluck('name', 'id')
                    )
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('category')
                    ->label(__('dashboard.type'))
                    ->options(CategoryEnum::options())
                    ->required()
                    ->native(false)
                    ->searchable(),

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
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('content.en')
                    ->label(__('dashboard.english_content'))
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('price')
                    ->label(__('dashboard.price'))
                    ->numeric()
                    ->required()
                    ->prefix('$'),

                Forms\Components\TextInput::make('final_price')
                    ->label(__('final price'))
                    ->numeric()
                    ->required()
                    ->prefix('$'),


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
                Tables\Columns\TextColumn::make('categoryService.name')
                    ->label(__('dashboard.category'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->label(__('dashboard.type'))
                    ->formatStateUsing(fn (CategoryEnum $state) => $state->label())
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('dashboard.name'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('price')
                    ->label(__('dashboard.price'))
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('final_price')
                    ->label(__('final price'))
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\BooleanColumn::make('is_active')
                    ->label(__('dashboard.active'))
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_service_id')
                    ->label(__('dashboard.category_service'))
                    ->options(CategoryService::all()->pluck('name', 'id'))
                    ->searchable(),
                Tables\Filters\SelectFilter::make('category')
                    ->label(__('dashboard.type'))
                    ->options(CategoryEnum::options())
                    ->searchable()
                    ->native(false),
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
                Tables\Actions\DeleteAction::make(),
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

            RelationManagers\OrdersRelationManager::class,];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
