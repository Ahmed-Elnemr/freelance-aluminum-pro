<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('chat')
                ->label(__('dashboard.chat'))
                ->url(fn () => route('filament.pages.chat-page', ['userId' => $this->record->user_id]))
                ->color('success')
                ->icon('heroicon-o-chat-bubble-left-right'),
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
