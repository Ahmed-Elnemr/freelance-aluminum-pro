<?php

namespace App\Filament\Resources\MaintenanceInspectionResource\Pages;

use App\Filament\Resources\MaintenanceInspectionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMaintenanceInspection extends EditRecord
{
    protected static string $resource = MaintenanceInspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
