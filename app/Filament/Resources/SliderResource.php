<?php

namespace App\Filament\Resources;

use App\Enum\SliderTypeEnum;
use App\Filament\Resources\SliderResource\Pages;
use App\Models\Slider;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SliderResource extends Resource
{
    use Translatable;

    protected static ?string $model = Slider::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    public static function getTranslatableLocales(): array
    {
        return ['ar', 'en'];
    }

    public static function getNavigationLabel(): string
    {
        return __('sliders');
    }

    public static function getModelLabel(): string
    {
        return __('slider');
    }

    public static function getPluralModelLabel(): string
    {
        return __('sliders');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name.ar')
                    ->label(__('arabic name'))
                    ->nullable(),

                Forms\Components\TextInput::make('name.en')
                    ->label(__('english name'))
                    ->nullable(),

                Forms\Components\Textarea::make('content.ar')
                    ->label(__('arabic content'))
                    ->nullable()
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('content.en')
                    ->label(__('english content'))
                    ->nullable()
                    ->columnSpanFull(),

                Forms\Components\Select::make('type')
                    ->label(__('type'))
                    ->options(SliderTypeEnum::options())
                    ->required()
                    ->native(false)
                    ->searchable(),

                SpatieMediaLibraryFileUpload::make('sliders')
                    ->collection('sliders')
                ->required()
                ->label(__('image')),

                Forms\Components\Toggle::make('is_active')
                    ->label(__('active'))
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
                Tables\Columns\TextColumn::make('name')
                    ->label(__('name'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('type')
                    ->label(__('type'))
                    ->formatStateUsing(fn (SliderTypeEnum $state) => $state->label())
                    ->sortable()
                    ->searchable(),

                Tables\Columns\BooleanColumn::make('is_active')
                    ->label(__('active'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('created_at'))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('sliders')
                    ->label(__('الصورة'))
                    ->getStateUsing(fn (Slider $record) => $record->getFirstMediaUrl('sliders'))
                    ->size(60),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('type'))
                    ->options(SliderTypeEnum::options())
                    ->native(false)
                    ->searchable(),

                Tables\Filters\SelectFilter::make('is_active')
                    ->label(__('activation'))
                    ->options([
                        1 => __('active'),
                        0 => __('inactive'),
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
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSliders::route('/'),
            'create' => Pages\CreateSlider::route('/create'),
            'edit' => Pages\EditSlider::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
