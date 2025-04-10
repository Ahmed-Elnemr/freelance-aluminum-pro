<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryServiceResource\Pages;
use App\Filament\Resources\CategoryServiceResource\RelationManagers;
use App\Models\CategoryService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoryServiceResource extends Resource
{
    use Translatable;

    protected static ?string $model = CategoryService::class;
    public static function getNavigationLabel(): string
    {
        return __('dashboard.category_services');
    }
    public static function getModelLabel(): string
    {
        return __('dashboard.category_service');
    }
    public static function getPluralModelLabel(): string
    {
        return __('dashboard.category_services');
    }
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    public static function getTranslatableLocales(): array
    {
        return ['ar','en' ];
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
                //
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategoryServices::route('/'),
            'create' => Pages\CreateCategoryService::route('/create'),
            'edit' => Pages\EditCategoryService::route('/{record}/edit'),
        ];
    }
}
