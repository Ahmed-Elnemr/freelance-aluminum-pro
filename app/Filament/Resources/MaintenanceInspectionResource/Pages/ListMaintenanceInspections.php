<?php

namespace App\Filament\Resources\MaintenanceInspectionResource\Pages;

use App\Filament\Resources\MaintenanceInspectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMaintenanceInspections extends ListRecords
{
    protected static string $resource = MaintenanceInspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
