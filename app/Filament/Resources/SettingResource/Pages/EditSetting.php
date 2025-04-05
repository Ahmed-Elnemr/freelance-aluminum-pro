<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Remove image before saving to DB
        unset($data['image']);
        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->data['type'] === 'image' && isset($this->data['image'])) {
            // Get the new image path
            $imagePath = reset($this->data['image']);

            // Clear the existing media collection before adding the new image
            if ($this->record->hasMedia('settings')) {
                $this->record->clearMediaCollection('settings');
            }

            // Add the new image to the media collection
            $this->record
                ->addMedia(public_path('storage/' . $imagePath))
                ->preservingOriginal()
                ->toMediaCollection('settings');
        }
    }
}
