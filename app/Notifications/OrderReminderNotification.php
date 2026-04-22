<?php

namespace App\Notifications;

use App\Actions\FCMAction;
use App\Models\Order;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderReminderNotification extends Notification
{
    use Queueable;

    protected $data;

    public function __construct(public Order $order)
    {
        $this->data['title'] = [
            'ar' => __('dashboard.notification_order_reminder_title', [], 'ar'),
            'en' => __('dashboard.notification_order_reminder_title', [], 'en'),
        ];

        $this->data['body'] = [
            'ar' => __('dashboard.notification_order_reminder_body', [], 'ar'),
            'en' => __('dashboard.notification_order_reminder_body', [], 'en'),
        ];

        $this->data['type'] = 'order';
        $this->data['model_id'] = $order->id;
    }

    public function via(object $notifiable): array
    {
        return ['database', FcmChannel::class];
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
            ->data(['type' => 'order', 'model_id' => $this->order->id])
            ->getDatabaseMessage();
    }

    public function toFcm($notifiable)
    {
        FCMAction::new($notifiable)
            ->withData($this->data)
            ->withTitle($this->data['title'][app()->getLocale()] ?? '')
            ->withBody($this->data['body'][app()->getLocale()] ?? '')
            ->sendMessage('tokens');
    }
}
