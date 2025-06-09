<?php

namespace App\Filament\Resources\MainServiceResource\RelationManagers;

use App\Enum\CategoryEnum;
use App\Enum\TypeEnum;
use App\Models\CategoryService;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServicesRelationManager extends RelationManager
{
    protected static string $relationship = 'services';
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
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('services connected');
    }


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                SpatieMediaLibraryFileUpload::make('services')
                    ->label(__('images'))
                    ->collection('services')
                    ->multiple()
                    ->reorderable()
                    ->appendFiles()
                    ->downloadable()
                    ->openable()
                    ->preserveFilenames()
                    ->image()
                    ->imageEditor()
                    ->columnSpanFull(),

                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Select::make('category_service_id')
                            ->label(__('dashboard.category_service'))
                            ->options(CategoryService::active()->pluck('name', 'id'))
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('category')
                            ->label(__('dashboard.type'))
                            ->options(CategoryEnum::options())
                            ->required()
                            ->native(false)
                            ->searchable(),

                        Forms\Components\Select::make('type')
                            ->label(__('list type'))
                            ->options(TypeEnum::options())
                            ->required()
                            ->native(false)
                            ->searchable(),
                    ]),

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
                    ->prefix('SAR'),

                Forms\Components\TextInput::make('final_price')
                    ->label(__('final price'))
                    ->numeric()
                    ->required()
                    ->prefix('SAR'),

                Forms\Components\Toggle::make('is_active')
                    ->label(__('dashboard.active'))
                    ->default(true)
                    ->onColor('success')
                    ->offColor('danger')
                    ->onIcon('heroicon-s-check')
                    ->offIcon('heroicon-s-x-mark')
                    ->inline(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
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

                Tables\Columns\TextColumn::make('type')
                    ->label(__('list type'))
                    ->formatStateUsing(fn (TypeEnum $state) => $state->label())
                    ->sortable()
                    ->searchable(),

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
                Tables\Filters\SelectFilter::make('category_service_id')
                    ->label(__('dashboard.category_service'))
                    ->options(CategoryService::all()->pluck('name', 'id'))
                    ->searchable(),

                Tables\Filters\SelectFilter::make('category')
                    ->label(__('dashboard.type'))
                    ->options(CategoryEnum::options())
                    ->searchable()
                    ->native(false),

                Tables\Filters\SelectFilter::make('type')
                    ->label(__('type list'))
                    ->options(TypeEnum::options())
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
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
