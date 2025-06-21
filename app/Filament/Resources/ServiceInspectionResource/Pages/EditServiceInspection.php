<?php

namespace App\Filament\Resources\ServiceInspectionResource\Pages;

use App\Filament\Resources\ServiceInspectionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditServiceInspection extends EditRecord
{
    protected static string $resource = ServiceInspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
