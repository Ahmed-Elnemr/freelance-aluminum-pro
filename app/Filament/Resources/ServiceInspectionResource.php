<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceInspectionResource\Pages;
use App\Models\ServiceInspection;
use App\Models\Service;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;

class ServiceInspectionResource extends Resource
{
    protected static ?string $model = ServiceInspection::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    public static function getNavigationSort(): ?int
    {
        return 10;
    }
    public static function getNavigationGroup(): ?string
    {
        return __('Services Management');
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

                Select::make('service_id')
                    ->label(__('dashboard.service'))
                    ->options(Service::active()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
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

                Tables\Columns\TextColumn::make('service.name')
                    ->label(__('dashboard.service'))
                    ->sortable()
                    ->searchable()
                    ->url(fn ($record) => route('filament.admin.resources.services.edit', ['record' => $record->service_id]))
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

                Tables\Filters\SelectFilter::make('service_id')
                    ->label(__('dashboard.service'))
                    ->options(Service::pluck('name', 'id'))
                    ->searchable(),
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


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceInspections::route('/'),
            'create' => Pages\CreateServiceInspection::route('/create'),
            'edit' => Pages\EditServiceInspection::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
//                SoftDeletingScope::class,
            ])->latest();
    }
}
