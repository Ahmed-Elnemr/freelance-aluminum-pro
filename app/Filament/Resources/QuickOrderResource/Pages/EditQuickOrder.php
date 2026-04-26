<?php

namespace App\Filament\Resources\QuickOrderResource\Pages;

use App\Filament\Resources\QuickOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQuickOrder extends EditRecord
{
    protected static string $resource = QuickOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
