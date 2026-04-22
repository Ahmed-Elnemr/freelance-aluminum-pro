<?php

namespace App\Notifications;

use App\Actions\FCMAction;
use App\Models\Order;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderStatusChangedNotification extends Notification
{
    use Queueable;

    protected $data;

    public function __construct(public Order $order)
    {
        $statusKey = 'status_'.$order->status->value;
        $titleKey = 'notification_order_'.$order->status->value.'_title';
        $bodyKey = 'notification_order_'.$order->status->value.'_body';

        $this->data['title'] = [
            'ar' => __($titleKey, [], 'ar') === $titleKey ? __('dashboard.status', [], 'ar').': '.$order->status->label() : __($titleKey, [], 'ar'),
            'en' => __($titleKey, [], 'en') === $titleKey ? __('dashboard.status', [], 'en').': '.$order->status->label() : __($titleKey, [], 'en'),
        ];

        $arBody = __($bodyKey, [], 'ar') === $bodyKey ? __('dashboard.order', [], 'ar').' #'.$order->id.' '.$order->status->label() : __($bodyKey, [], 'ar');
        $enBody = __($bodyKey, [], 'en') === $bodyKey ? __('dashboard.order', [], 'en').' #'.$order->id.' '.$order->status->label() : __($bodyKey, [], 'en');

        if ($order->status->value === 'approved') {
            $startTime = substr($order->scheduled_time, 0, 5);
            $endTime = $order->end_time ? substr($order->end_time, 0, 5) : null;

            if ($endTime) {
                $arBody .= " (من $startTime إلى $endTime)";
                $enBody .= " (from $startTime to $endTime)";
            } else {
                $arBody .= " (الساعة $startTime)";
                $enBody .= " (at $startTime)";
            }
        }

        $this->data['body'] = [
            'ar' => $arBody,
            'en' => $enBody,
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
