<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminOrderNotification extends Notification
{
    use Queueable;

    protected $data;

    public function __construct(public Order $order, public string $action = 'created')
    {
        $user_name = $order->user?->name;

        if ($action === 'created') {
            $this->data['title'] = [
                'ar' => __('dashboard.notification_new_order_admin_title', [], 'ar'),
                'en' => __('dashboard.notification_new_order_admin_title', [], 'en'),
            ];
            $this->data['body'] = [
                'ar' => __('dashboard.notification_new_order_admin_body', ['user_name' => $user_name], 'ar'),
                'en' => __('dashboard.notification_new_order_admin_body', ['user_name' => $user_name], 'en'),
            ];
        } else {
            $this->data['title'] = [
                'ar' => __('dashboard.notification_order_cancelled_by_user_title', [], 'ar'),
                'en' => __('dashboard.notification_order_cancelled_by_user_title', [], 'en'),
            ];
            $this->data['body'] = [
                'ar' => __('dashboard.notification_order_cancelled_by_user_body', ['user_name' => $user_name], 'ar'),
                'en' => __('dashboard.notification_order_cancelled_by_user_body', ['user_name' => $user_name], 'en'),
            ];
        }

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
