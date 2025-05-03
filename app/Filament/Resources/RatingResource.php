<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RatingResource\Pages\ListRatings;
use App\Models\Rating;
use App\Models\User;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RatingResource extends Resource
{
    protected static ?string $model = Rating::class;
    protected static ?string $navigationIcon = 'heroicon-o-star';

    public static function getNavigationLabel(): string
    {
        return __('Ratings');
    }

    public static function getModelLabel(): string
    {
        return __('Rate');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Ratings');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where(function ($query) {
                $query->where('rateable_type', '!=', Service::class)
                    ->orWhereHasMorph(
                        'rateable',
                        [Service::class],
                        function (Builder $query) {
                            $query->whereNull('deleted_at');
                        }
                    );
            })
            ->latest();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRatings::route('/'),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('User'))
                    ->searchable()
                    ->url(fn($record) => route('filament.admin.resources.users.edit', ['record' => $record->user_id]))
                    ->openUrlInNewTab()
                    ->color('primary')
                ,
                Tables\Columns\TextColumn::make('rating')
                    ->label(__('Rating'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('rateable_type')
                    ->label(__('Rateable Name'))
                    ->formatStateUsing(function ($state, $record) {
                        if ($state === Service::class) {
                            $service = $record->rateable;

                            if ($service && !$service->trashed()) {
                                return $service->name;
                            }

                            return null; // لا تعرض شيء إذا كانت الخدمة ممسوحة
                        }

                        return $state;
                    }),

            ])
            ->actions([
//                Tables\Actions\EditAction::make(),
//                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
