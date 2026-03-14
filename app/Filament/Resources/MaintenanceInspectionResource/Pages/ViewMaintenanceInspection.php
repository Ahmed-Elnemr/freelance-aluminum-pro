<?php

namespace App\Filament\Resources\MaintenanceInspectionResource\Pages;

use App\Filament\Resources\MaintenanceInspectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMaintenanceInspection extends ViewRecord
{
    protected static string $resource = MaintenanceInspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
