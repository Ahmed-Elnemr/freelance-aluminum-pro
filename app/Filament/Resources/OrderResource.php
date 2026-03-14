<?php

namespace App\Filament\Resources;

use App\Enum\OrderStatusEnum;
use App\Enum\UserTypeEnum;
use App\Filament\Resources\OrderResource\Pages;
use App\Models\Maintenance;
use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderCompletedNotification;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::active()->where('status', OrderStatusEnum::CURRENT->value)->count() ?? 0;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::active()->where('status', OrderStatusEnum::CURRENT->value)->count() > 10 ? 'warning' : 'primary';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('Current Orders');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Services Management');
    }

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
                            ->options(
                                User::where('type', UserTypeEnum::CLIENT->value)
                                    ->active()
                                    ->latest()
                                    ->get()
                                    ->pluck('name', 'id')
                                    ->filter(fn ($label) => ! is_null($label))
                            )
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('maintenance_id')
                            ->label(__('dashboard.maintenance'))
                            ->options(
                                Maintenance::active()->latest()->get()->pluck('name', 'id')
                                    ->filter(fn ($label) => ! is_null($label))
                            )
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
                    ])->columns(2),

                Section::make(__('location info'))
                    ->schema([
                        TextInput::make('latitude')
                            ->label(__('dashboard.latitude'))
                            ->numeric()
                            ->required()
                            ->minValue(-90)
                            ->maxValue(90),

                        TextInput::make('longitude')
                            ->label(__('dashboard.longitude'))
                            ->numeric()
                            ->required()
                            ->minValue(-180)
                            ->maxValue(180),

                        TextInput::make('location_name')
                            ->label(__('dashboard.location_name'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('map_link')
                            ->label(__('map link'))
                            ->formatStateUsing(fn (?Order $record): string => $record && $record->latitude && $record->longitude
                                ? 'https://www.google.com/maps?q='.$record->latitude.','.$record->longitude
                                : '-')
                            ->disabled()
                            ->dehydrated(false)
                            ->suffixAction(
                                Action::make('open_map')
                                    ->label(__('open map'))
                                    ->icon('heroicon-o-arrow-top-right-on-square')
                                    ->url(fn (?Order $record): string => $record && $record->latitude && $record->longitude
                                        ? 'https://www.google.com/maps?q='.$record->latitude.','.$record->longitude
                                        : '#')
                                    ->openUrlInNewTab()
                                    ->visible(fn (?Order $record) => $record && $record->latitude && $record->longitude)
                            )
                            ->columnSpanFull()
                            ->visible(fn (?Order $record) => $record && $record->latitude && $record->longitude),
                    ])
                    ->columns(2),

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

                        Forms\Components\Textarea::make('internal_note')
                            ->label(__('dashboard.internal_note'))
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Section::make(__('media'))
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('media')
                            ->label(__('images and videos'))
                            ->collection('media')
                            ->multiple()
                            ->reorderable()
                            ->downloadable()
                            ->openable()
                            ->previewable(true)
                            ->responsiveImages()
                            ->imageEditor()
                            ->imageEditorEmptyFillColor('#000000')
                            ->maxSize(256 * 10240)
                            ->maxFiles(10)
                            ->acceptedFileTypes([
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                                'video/mp4',
                                'video/quicktime',
                            ])
                            ->imagePreviewHeight('250')
                            ->panelLayout('grid')
                            ->appendFiles()
                            ->columnSpanFull(),

                        SpatieMediaLibraryFileUpload::make('sounds')
                            ->label(__('dashboard.audio_records'))
                            ->collection('sounds')
                            ->multiple()
                            ->reorderable()
                            ->downloadable()
                            ->openable()
                            ->previewable(false)
                            ->maxSize(10240)
                            ->maxFiles(10)
                            ->acceptedFileTypes([
                                'audio/mpeg',
                                'audio/wav',
                                'audio/x-wav',
                                'audio/x-m4a',
                                'audio/ogg',
                            ])
                            ->appendFiles()
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('dashboard.user'))
                    ->sortable()
                    ->searchable()
                    ->url(fn ($record) => UserResource::getUrl('edit', ['record' => $record->user_id]))
                    ->openUrlInNewTab()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('maintenance.name')
                    ->label(__('dashboard.maintenance'))
                    ->formatStateUsing(function ($state, $record) {
                        return $record->maintenance?->getTranslation('name', 'ar')."\n".$record->maintenance?->getTranslation('name', 'en');
                    })
                    ->html()
                    ->sortable()
                    ->searchable()
                    ->url(fn ($record) => MaintenanceResource::getUrl('edit', ['record' => $record->maintenance_id]))
                    ->openUrlInNewTab()
                    ->color('info'),

                Tables\Columns\TextColumn::make('scheduled_date')
                    ->label(__('dashboard.date'))
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('formatted_time')
                    ->label(__('dashboard.time'))
                    ->sortable(['scheduled_time']),

                Tables\Columns\TextColumn::make('location_name')
                    ->label(__('dashboard.location_name'))
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->location_name),

                SpatieMediaLibraryImageColumn::make('media')
                    ->label(__('media'))
                    ->collection('media')
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText()
                    ->extraImgAttributes(['class' => 'object-cover']),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('dashboard.status'))
                    ->badge()
                    ->formatStateUsing(fn (OrderStatusEnum $state) => $state->label())
                    ->color(fn (OrderStatusEnum $state): string => match ($state) {
                        OrderStatusEnum::CURRENT => 'success',
                        OrderStatusEnum::EXPIRED => 'danger',
                    })
                    ->sortable()
                    ->searchable(),

                Tables\Columns\BooleanColumn::make('is_active')
                    ->label(__('dashboard.active'))
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->label(__('dashboard.user'))
                    ->options(
                        User::all()->pluck('name', 'id')->filter(fn ($label) => ! is_null($label))
                    )
                    ->searchable(),

                Tables\Filters\SelectFilter::make('maintenance_id')
                    ->label(__('dashboard.maintenance'))
                    ->options(
                        Maintenance::all()->pluck('name', 'id')->filter(fn ($label) => ! is_null($label))
                    )
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
                Tables\Actions\Action::make('chat')
                    ->label(__('dashboard.chat'))
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->url(fn ($record) => route('filament.pages.chat-page', ['userId' => $record->user_id]))
                    ->tooltip(__('dashboard.chat'))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('view_user')
                    ->label(__('dashboard.user'))
                    ->icon('heroicon-o-user')
                    ->color('info')
                    ->url(fn ($record) => UserResource::getUrl('edit', ['record' => $record->user_id]))
                    ->tooltip(__('dashboard.user'))
                    ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make(),
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
            'view' => Pages\ViewOrder::route('/{record}'),
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
