<?php

namespace App\Filament\Resources;

use App\Enum\GalleryTypeEnum;
use App\Filament\Resources\GalleryResource\Pages;
use App\Models\Gallery;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GalleryResource extends Resource
{
    use Translatable;

    protected static ?string $model = Gallery::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    public static function getTranslatableLocales(): array
    {
        return ['ar', 'en'];
    }

    public static function getNavigationSort(): ?int
    {
        return 6;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('dashboard.gallery');
    }

    public static function getModelLabel(): string
    {
        return __('dashboard.gallery_item');
    }

    public static function getPluralModelLabel(): string
    {
        return __('dashboard.galleries');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title.ar')
                    ->label(__('dashboard.arabic_title'))
                    ->maxLength(255),

                Forms\Components\TextInput::make('title.en')
                    ->label(__('dashboard.english_title'))
                    ->maxLength(255),

                SpatieMediaLibraryFileUpload::make('gallery')
                    ->label(__('dashboard.gallery_file'))
                    ->collection('gallery')
                    ->required()
                    ->downloadable()
                    ->openable()
                    ->previewable(true)
                    ->maxSize(51200)
                    ->acceptedFileTypes([
                        'image/jpeg',
                        'image/png',
                        'image/webp',
                        'image/gif',
                        'video/mp4',
                        'video/quicktime',
                        'video/webm',
                    ])
                    ->helperText(__('dashboard.gallery_file_helper'))
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('is_active')
                    ->label(__('dashboard.active'))
                    ->default(true)
                    ->onColor('success')
                    ->offColor('danger')
                    ->inline(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('preview')
                    ->label(__('dashboard.gallery_file'))
                    ->getStateUsing(fn (Gallery $record): ?string => $record->isImage()
                        ? $record->getFirstMediaUrl('gallery')
                        : null)
                    ->square()
                    ->size(72),

                Tables\Columns\TextColumn::make('title')
                    ->label(__('dashboard.gallery_title'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label(__('dashboard.gallery_type'))
                    ->badge()
                    ->formatStateUsing(fn (GalleryTypeEnum $state): string => $state->label())
                    ->color(fn (GalleryTypeEnum $state): string => $state === GalleryTypeEnum::Video ? 'warning' : 'success')
                    ->sortable(),

                Tables\Columns\BooleanColumn::make('is_active')
                    ->label(__('dashboard.active'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label(__('dashboard.sort_order'))
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('dashboard.gallery_type'))
                    ->options(GalleryTypeEnum::options())
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
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGalleries::route('/'),
            'create' => Pages\CreateGallery::route('/create'),
            'edit' => Pages\EditGallery::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
