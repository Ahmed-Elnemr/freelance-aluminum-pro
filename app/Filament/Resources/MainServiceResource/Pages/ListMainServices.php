<?php

namespace App\Filament\Resources\MainServiceResource\Pages;

use App\Filament\Resources\MainServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMainServices extends ListRecords
{
    use ListRecords\Concerns\Translatable;

    protected static string $resource = MainServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\LocaleSwitcher::make()

        ];
    }
}
