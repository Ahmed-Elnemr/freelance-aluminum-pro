<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MainServiceResource\Pages;
use App\Models\MainService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class MainServiceResource extends Resource
{
    use Translatable;
    public static function getTranslatableLocales(): array
    {
        return ['ar','en' ];
    }
    public static function getNavigationSort(): ?int
    {
        return 1;
    }
    protected static ?string $model = MainService::class;

    public static function getNavigationGroup(): ?string
    {
        return __('Services Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('dashboard.main_services');
    }

    public static function getModelLabel(): string
    {
        return __('dashboard.main_service');
    }

    public static function getPluralModelLabel(): string
    {
        return __('dashboard.main_services');
    }

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                SpatieMediaLibraryFileUpload::make('main_services')
                    ->label(__('images'))
                    ->collection('main_services')
                    ->image()
                    ->preserveFilenames()
                    ->openable()
                    ->downloadable()
                    ->columnSpanFull(),

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

                Forms\Components\Toggle::make('is_active')
                    ->label(__('dashboard.active'))
                    ->default(true)
                    ->onColor('success')
                    ->offColor('danger'),
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
            \App\Filament\Resources\MainServiceResource\RelationManagers\ServicesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMainServices::route('/'),
            'create' => Pages\CreateMainService::route('/create'),
            'edit' => Pages\EditMainService::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class])
            ->latest();
    }
}
