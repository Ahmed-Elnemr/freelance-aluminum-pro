<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSetting extends CreateRecord
{

//    use CreateRecord\Concerns\Translatable;
    protected static string $resource = SettingResource::class;

//    protected function getHeaderActions(): array
//    {
//        return [
//            Actions\LocaleSwitcher::make(),
//            // ...
//        ];
//    }
    protected function mutateFormDataBeforeCreate(array $data): array
    {

        // Remove image before saving to DB
        unset($data['image']);
        return $data;
    }



    protected function afterCreate(): void
    {
        if ($this->data['type'] === 'image' && $this->data['image']) {
            $imagePath = reset($this->data['image']);

            $this->record
                ->addMedia(public_path('storage/' . $imagePath))
                ->preservingOriginal()
                ->toMediaCollection('settings');
        }
    }
}
