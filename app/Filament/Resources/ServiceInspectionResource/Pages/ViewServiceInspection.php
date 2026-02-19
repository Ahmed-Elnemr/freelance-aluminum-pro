<?php

namespace App\Filament\Resources\ServiceInspectionResource\Pages;

use App\Filament\Resources\ServiceInspectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewServiceInspection extends ViewRecord
{
    protected static string $resource = ServiceInspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
