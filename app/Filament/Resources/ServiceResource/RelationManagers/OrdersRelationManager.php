<?php

namespace App\Filament\Resources\ServiceResource\RelationManagers;

use App\Enum\OrderStatusEnum;
use App\Models\User;
use Filament\Forms;
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
                Forms\Components\Select::make('user_id')
                    ->label(__('dashboard.user'))
                    ->options(User::where('type', \App\Enum\UserTypeEnum::CLIENT->value)
                        ->active()
                        ->pluck('name', 'id')
                        ->filter())
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('google_maps_url')
                    ->label(__('dashboard.location_url'))
                    ->placeholder('https://maps.app.goo.gl/...')
                    ->required(),

                Forms\Components\Textarea::make('description.ar')
                    ->label(__('dashboard.arabic_description')),

                Forms\Components\Textarea::make('description.en')
                    ->label(__('dashboard.english_description')),

                Forms\Components\Select::make('status')
                    ->label(__('dashboard.status'))
                    ->options(OrderStatusEnum::options())
                    ->required()
                    ->native(false),

                Forms\Components\Toggle::make('is_active')
                    ->label(__('dashboard.active'))
                    ->default(true)
                    ->onColor('success')
                    ->offColor('danger'),
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
                    ->options(User::where('type', \App\Enum\UserTypeEnum::CLIENT->value)
                        ->active()
                        ->pluck('name', 'id')
                        ->filter())
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
