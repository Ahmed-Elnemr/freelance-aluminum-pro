<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuickOrderResource\Pages;
use App\Models\QuickOrder;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class QuickOrderResource extends Resource
{
    protected static ?string $model = QuickOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    public static function getNavigationGroup(): ?string
    {
        return __('Services Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('dashboard.quick_orders');
    }

    public static function getModelLabel(): string
    {
        return __('dashboard.quick_order');
    }

    public static function getPluralModelLabel(): string
    {
        return __('dashboard.quick_orders');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('dashboard.order_information'))
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label(__('dashboard.user'))
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required(),

                        Forms\Components\Textarea::make('message')
                            ->label(__('dashboard.message'))
                            ->columnSpanFull(),

                        SpatieMediaLibraryFileUpload::make('sounds')
                            ->label(__('dashboard.sounds'))
                            ->collection('sounds')
                            ->multiple()
                            ->downloadable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('dashboard.user'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.mobile')
                    ->label(__('dashboard.mobile'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('message')
                    ->label(__('dashboard.message'))
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\TextColumn::make('sounds_count')
                    ->label(__('dashboard.sounds'))
                    ->counts('media')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('dashboard.date'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->label(__('dashboard.user'))
                    ->relationship('user', 'name')
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
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListQuickOrders::route('/'),
            'create' => Pages\CreateQuickOrder::route('/create'),
            'view' => Pages\ViewQuickOrder::route('/{record}'),
            'edit' => Pages\EditQuickOrder::route('/{record}/edit'),
        ];
    }
}
