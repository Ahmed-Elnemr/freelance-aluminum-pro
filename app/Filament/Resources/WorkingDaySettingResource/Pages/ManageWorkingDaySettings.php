<?php

namespace App\Filament\Resources\WorkingDaySettingResource\Pages;

use App\Filament\Resources\WorkingDaySettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageWorkingDaySettings extends ManageRecords
{
    protected static string $resource = WorkingDaySettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
