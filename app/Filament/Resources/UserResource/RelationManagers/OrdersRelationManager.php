<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Enum\OrderStatusEnum;
use App\Models\Maintenance;
use App\Models\Order;
use App\Notifications\OrderCompletedNotification;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
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
                        Forms\Components\Select::make('maintenance_id')
                            ->label(__('dashboard.maintenance'))
                            ->options(Maintenance::active()
                                ->pluck('name', 'id')
                                ->filter(fn ($label) => ! is_null($label)))
                            ->searchable()
                            ->required(),

                        Forms\Components\DatePicker::make('scheduled_date')
                            ->label(__('dashboard.date'))
                            ->required()
                            ->native(false)
                            ->displayFormat('Y-m-d'),

                        Forms\Components\TimePicker::make('scheduled_time')
                            ->label(__('dashboard.time'))
                            ->required()
                            ->native(false)
                            ->displayFormat('H:i'),
                    ])->columns(3),

                Forms\Components\Section::make(__('dashboard.location'))
                    ->schema([
                        Forms\Components\TextInput::make('latitude')
                            ->label(__('dashboard.latitude'))
                            ->numeric()
                            ->required()
                            ->minValue(-90)
                            ->maxValue(90),

                        Forms\Components\TextInput::make('longitude')
                            ->label(__('dashboard.longitude'))
                            ->numeric()
                            ->required()
                            ->minValue(-180)
                            ->maxValue(180),

                        Forms\Components\TextInput::make('location_name')
                            ->label(__('dashboard.location_name'))
                            ->required()
                            ->maxLength(255),
                    ])->columns(3),

                Forms\Components\Section::make(__('dashboard.additional_info'))
                    ->schema([
                        Forms\Components\Textarea::make('description.ar')
                            ->label(__('dashboard.arabic_description'))
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('description.en')
                            ->label(__('dashboard.english_description'))
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('internal_note')
                            ->label(__('dashboard.internal_note'))
                            ->rows(4)
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

                Forms\Components\Section::make(__('media'))
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('media')
                            ->label(__('images and videos'))
                            ->collection('media')
                            ->multiple()
                            ->reorderable()
                            ->downloadable()
                            ->openable()
                            ->image()
                            ->maxSize(256 * 10240)
                            ->maxFiles(10)
                            ->acceptedFileTypes([
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                                'video/mp4',
                                'video/quicktime',
                            ])
                            ->columnSpanFull(),

                        SpatieMediaLibraryFileUpload::make('sounds')
                            ->label(__('dashboard.audio_records'))
                            ->collection('sounds')
                            ->multiple()
                            ->reorderable()
                            ->downloadable()
                            ->maxSize(10240)
                            ->maxFiles(10)
                            ->acceptedFileTypes([
                                'audio/mpeg',
                                'audio/wav',
                                'audio/x-wav',
                                'audio/x-m4a',
                                'audio/ogg',
                            ])
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('maintenance.name')
                    ->label(__('dashboard.maintenance'))
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
                Tables\Filters\SelectFilter::make('maintenance_id')
                    ->label(__('dashboard.maintenance'))
                    ->options(Maintenance::all()->pluck('name', 'id')->filter())
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
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = $this->getOwnerRecord()->id;

                        return $data;
                    }),
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
