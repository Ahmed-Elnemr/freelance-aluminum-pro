<?php

namespace App\Filament\Resources\ServiceInspectionResource\Pages;

use App\Filament\Resources\ServiceInspectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListServiceInspections extends ListRecords
{
    protected static string $resource = ServiceInspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
