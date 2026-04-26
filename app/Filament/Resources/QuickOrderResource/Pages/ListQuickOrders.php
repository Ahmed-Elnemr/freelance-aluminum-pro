<?php

namespace App\Filament\Resources\QuickOrderResource\Pages;

use App\Filament\Resources\QuickOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQuickOrders extends ListRecords
{
    protected static string $resource = QuickOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
