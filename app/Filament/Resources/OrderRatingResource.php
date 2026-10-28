<?php

namespace App\Filament\Resources;

use App\Enum\OrderRatingTag;
use App\Filament\Resources\OrderRatingResource\Pages;
use App\Models\Maintenance;
use App\Models\OrderRating;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderRatingResource extends Resource
{
    protected static ?string $model = OrderRating::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    public static function getNavigationSort(): ?int
    {
        return 7;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('ratings.order_ratings');
    }

    public static function getModelLabel(): string
    {
        return __('ratings.order_rating');
    }

    public static function getPluralModelLabel(): string
    {
        return __('ratings.order_ratings');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'order', 'maintenance'])
            ->latest();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_id')
                    ->label(__('dashboard.order'))
                    ->searchable()
                    ->sortable()
                    ->url(fn (OrderRating $record): string => OrderResource::getUrl('view', ['record' => $record->order_id]))
                    ->color('primary'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('dashboard.user'))
                    ->searchable()
                    ->url(fn (OrderRating $record): ?string => $record->user_id
                        ? route('filament.admin.resources.users.edit', ['record' => $record->user_id])
                        : null)
                    ->color('primary'),

                Tables\Columns\TextColumn::make('maintenance.name')
                    ->label(__('dashboard.service')),

                Tables\Columns\TextColumn::make('stars')
                    ->label(__('ratings.stars'))
                    ->badge()
                    ->color(fn (int $state): string => $state <= 3 ? 'danger' : 'success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tags_label')
                    ->label(__('ratings.tags'))
                    ->getStateUsing(function (OrderRating $record): string {
                        return collect($record->tags ?? [])
                            ->map(fn (string $tag): string => OrderRatingTag::tryFrom($tag)?->label() ?? $tag)
                            ->implode('، ');
                    }),

                Tables\Columns\TextColumn::make('comment')
                    ->label(__('ratings.comment'))
                    ->limit(80)
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('dashboard.date'))
                    ->dateTime()
                    ->sortable(),

                ToggleColumn::make('is_hidden')
                    ->label(__('ratings.hide_comment')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('stars')
                    ->label(__('ratings.stars'))
                    ->options([
                        1 => '1',
                        2 => '2',
                        3 => '3',
                        4 => '4',
                        5 => '5',
                    ]),

                Tables\Filters\SelectFilter::make('maintenance_id')
                    ->label(__('dashboard.service'))
                    ->options(
                        Maintenance::query()->get()->pluck('name', 'id')->filter(fn ($label) => ! is_null($label))
                    )
                    ->searchable(),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label(__('dashboard.from')),
                        Forms\Components\DatePicker::make('created_until')
                            ->label(__('dashboard.to')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageOrderRatings::route('/'),
        ];
    }
}
