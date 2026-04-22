<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminOrderFinishedNotification extends Notification
{
    use Queueable;

    protected $data;

    public function __construct(public Order $order)
    {
        $this->data['title'] = [
            'ar' => __('dashboard.notification_order_finished_admin_title', [], 'ar'),
            'en' => __('dashboard.notification_order_finished_admin_title', [], 'en'),
        ];

        $this->data['body'] = [
            'ar' => __('dashboard.notification_order_finished_admin_body', ['order_id' => $order->id], 'ar'),
            'en' => __('dashboard.notification_order_finished_admin_body', ['order_id' => $order->id], 'en'),
        ];

        $this->data['type'] = 'order';
        $this->data['model_id'] = $order->id;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return $this->data;
    }

    public function toDatabase(object $notifiable): array
    {
        return \Filament\Notifications\Notification::make()
            ->title($this->data['title'][app()->getLocale()] ?? '')
            ->body($this->data['body'][app()->getLocale()] ?? '')
            ->actions([
                \Filament\Notifications\Actions\Action::make('view')
                    ->label(__('dashboard.view'))
                    ->url(\App\Filament\Resources\OrderResource::getUrl('view', ['record' => $this->order])),
            ])
            ->data(['type' => 'order', 'model_id' => $this->order->id])
            ->getDatabaseMessage();
    }
}
