<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use App\Models\Order;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditService extends EditRecord
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];

    }

    protected function beforeDelete()
    {
        $activeOrdersCount = Order::query()
            ->where('service_id', $this->record->id) // هنا بحط العمود اللي بيربط الخدمة بالأوردر
            ->whereNull('deleted_at') // أتأكد إنه مش محذوف
            ->count();

        if ($activeOrdersCount > 0) {
            Notification::make()
                ->title('لا يمكنك حذف هذه الخدمة')
                ->body('هذه الخدمة مرتبطة بطلبات قائمة.')
                ->danger()
                ->send();

            $this->halt(); // وقف عملية الحذف
        }
    }

}
