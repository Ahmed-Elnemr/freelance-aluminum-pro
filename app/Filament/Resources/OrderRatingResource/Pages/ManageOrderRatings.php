<?php

namespace App\Filament\Resources\OrderRatingResource\Pages;

use App\Filament\Resources\OrderRatingResource;
use Filament\Resources\Pages\ManageRecords;

class ManageOrderRatings extends ManageRecords
{
    protected static string $resource = OrderRatingResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
