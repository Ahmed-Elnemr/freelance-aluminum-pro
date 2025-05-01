<?php

namespace App\Filament\Resources;

use App\Enum\OrderStatusEnum;
use App\Enum\UserTypeEnum;
use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use App\Notifications\OrderCompletedNotification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    public static function getNavigationLabel(): string
    {
        return __('dashboard.orders');
    }

    public static function getModelLabel(): string
    {
        return __('dashboard.order');
    }

    public static function getPluralModelLabel(): string
    {
        return __('dashboard.orders');
    }

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('dashboard.order_information'))
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label(__('dashboard.user'))
                            ->options(User::where('type', UserTypeEnum::CLIENT->value)
                                ->active()
                                ->get()
                                ->pluck('name', 'id')
                                ->filter(fn($label) => !is_null($label)))
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('service_id')
                            ->label(__('dashboard.service'))
                            ->options(Service::active()->get()->pluck('name', 'id')
                                ->filter(fn($label) => !is_null($label)))
                            ->searchable()
                            ->required(),
                    ])->columns(3),

                Section::make(__('dashboard.location'))
                    ->schema([
                        Forms\Components\TextInput::make('google_maps_url')
                            ->label(__('dashboard.location_url'))
                            ->placeholder('https://maps.app.goo.gl/...')
                            ->columnSpanFull()
                            ->required(),
                    ]),

                Section::make(__('dashboard.additional_info'))
                    ->schema([
                        Forms\Components\Textarea::make('description.ar')
                            ->label(__('dashboard.arabic_description'))
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('description.en')
                            ->label(__('dashboard.english_description'))
                            ->columnSpanFull(),

                        Forms\Components\Select::make('status')
                            ->label(__('dashboard.status'))
                            ->options(OrderStatusEnum::options())
                            ->required()
                            ->native(false)
                            ->afterStateUpdated(function ($state, $set, $get, ?Order $record) {
                                if ($record && $state === 'expired') {
                                    $record->user->notify(new OrderCompletedNotification($record));
                                }
                            }),

                        Forms\Components\Toggle::make('is_active')
                            ->label(__('dashboard.active'))
                            ->default(true)
                            ->onColor('success')
                            ->offColor('danger'),
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

                Tables\Columns\TextColumn::make('service.name')
                    ->label(__('dashboard.service'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('dashboard.status'))
                    ->formatStateUsing(fn (OrderStatusEnum $state) => $state->label())
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('location_name')
                    ->label(__('dashboard.location_name'))
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->location_name),

                Tables\Columns\BooleanColumn::make('is_active')
                    ->label(__('dashboard.active'))
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->label(__('dashboard.user'))
                    ->options(User::all()->pluck('name', 'id')
                        ->filter(fn($label) => !is_null($label)))
                    ->searchable(),

                Tables\Filters\SelectFilter::make('service_id')
                    ->label(__('dashboard.service'))
                    ->options(Service::all()->pluck('name', 'id')
                        ->filter(fn($label) => !is_null($label)))
                    ->searchable(),

                Tables\Filters\SelectFilter::make('status')
                    ->label(__('dashboard.status'))
                    ->options(OrderStatusEnum::options())
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
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
