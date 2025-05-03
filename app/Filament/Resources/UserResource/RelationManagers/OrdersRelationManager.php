<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Enum\OrderStatusEnum;
use App\Enum\UserTypeEnum;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use App\Notifications\OrderCompletedNotification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';
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
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('orders connected');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('dashboard.order_information'))
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label(__('dashboard.user'))
                            ->options(User::where('type', UserTypeEnum::CLIENT->value)
                                ->active()
                                ->pluck('name', 'id')
                                ->filter(fn ($label) => !is_null($label)))
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('service_id')
                            ->label(__('dashboard.service'))
                            ->options(Service::active()
                                ->pluck('name', 'id')
                                ->filter(fn ($label) => !is_null($label)))
                            ->searchable()
                            ->required(),
                    ])->columns(3),

                Forms\Components\Section::make(__('dashboard.location'))
                    ->schema([
                        Forms\Components\TextInput::make('google_maps_url')
                            ->label(__('dashboard.location_url'))
                            ->placeholder('https://maps.app.goo.gl/...')
                            ->columnSpanFull()
                            ->required(),
                    ]),

                Forms\Components\Section::make(__('dashboard.additional_info'))
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
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
                    ->formatStateUsing(fn ($state) => $state instanceof OrderStatusEnum ? $state->label() : OrderStatusEnum::tryFrom($state)?->label() ?? '-')
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
                Tables\Filters\SelectFilter::make('service_id')
                    ->label(__('dashboard.service'))
                    ->options(Service::all()->pluck('name', 'id')->filter())
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
                ]),
            ]);
    }
}
