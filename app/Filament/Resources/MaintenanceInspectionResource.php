<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaintenanceInspectionResource\Pages;
use App\Models\Maintenance;
use App\Models\MaintenanceInspection;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MaintenanceInspectionResource extends Resource
{
    protected static ?string $model = MaintenanceInspection::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function getNavigationSort(): ?int
    {
        return 10;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('dashboard.maintenance');
    }

    public static function getNavigationLabel(): string
    {
        return __('dashboard.service_inspections');
    }

    public static function getModelLabel(): string
    {
        return __('dashboard.service_inspection');
    }

    public static function getPluralModelLabel(): string
    {
        return __('dashboard.service_inspections');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label(__('dashboard.user'))
                    ->options(User::whereType('client')->pluck('name', 'id'))
                    ->searchable()
                    ->required(),

                Select::make('maintenance_id')
                    ->label(__('dashboard.maintenance'))
                    ->options(Maintenance::active()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('user.name')
                            ->label(__('dashboard.user'))
                            ->url(fn ($record) => UserResource::getUrl('edit', ['record' => $record->user_id]))
                            ->openUrlInNewTab()
                            ->color('primary')
                            ->weight('bold'),

                        TextEntry::make('maintenance.name')
                            ->label(__('dashboard.maintenance'))
                            ->url(fn ($record) => MaintenanceResource::getUrl('edit', ['record' => $record->maintenance_id]))
                            ->openUrlInNewTab()
                            ->color('info')
                            ->weight('bold'),

                        TextEntry::make('created_at')
                            ->label(__('dashboard.inspected_at'))
                            ->dateTime()
                            ->color('gray'),
                    ])->columns(2),
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
                    ->url(fn ($record) => route('filament.admin.resources.users.edit', ['record' => $record->user_id]))
                    ->openUrlInNewTab()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('maintenance.name')
                    ->label(__('dashboard.maintenance'))
                    ->sortable()
                    ->searchable()
                    ->url(fn ($record) => route('filament.admin.resources.maintenances.edit', ['record' => $record->maintenance_id]))
                    ->openUrlInNewTab()
                    ->color('info'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('dashboard.inspected_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->label(__('dashboard.user'))
                    ->options(User::pluck('name', 'id'))
                    ->searchable(),

                Tables\Filters\SelectFilter::make('maintenance_id')
                    ->label(__('dashboard.maintenance'))
                    ->options(Maintenance::pluck('name', 'id'))
                    ->searchable(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMaintenanceInspections::route('/'),
            'create' => Pages\CreateMaintenanceInspection::route('/create'),
            'view' => Pages\ViewMaintenanceInspection::route('/{record}'),
            'edit' => Pages\EditMaintenanceInspection::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->latest();
    }
}
