<?php

namespace App\Filament\Resources\MainServiceResource\Pages;

use App\Filament\Resources\MainServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMainService extends EditRecord
{
    protected static string $resource = MainServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
