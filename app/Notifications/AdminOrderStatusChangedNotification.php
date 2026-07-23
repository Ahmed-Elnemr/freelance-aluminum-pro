<?php

namespace App\Notifications;

use App\Actions\FCMAction;
use App\Enum\OrderStatusEnum;
use App\Models\Order;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminOrderStatusChangedNotification extends Notification
{
    use Queueable;

    protected array $data;

    public function __construct(public Order $order, public string|OrderStatusEnum|null $status = null)
    {
        $statusEnum = $this->resolveStatus();
        $userName = $order->user?->name;
        $statusLabelAr = $this->statusLabel($statusEnum, 'ar');
        $statusLabelEn = $this->statusLabel($statusEnum, 'en');

        $this->data['title'] = [
            'ar' => __('dashboard.notification_order_status_changed_admin_title', [], 'ar'),
            'en' => __('dashboard.notification_order_status_changed_admin_title', [], 'en'),
        ];

        $this->data['body'] = [
            'ar' => __('dashboard.notification_order_status_changed_admin_body', [
                'user_name' => $userName,
                'order_id' => $order->id,
                'status' => $statusLabelAr,
            ], 'ar'),
            'en' => __('dashboard.notification_order_status_changed_admin_body', [
                'user_name' => $userName,
                'order_id' => $order->id,
                'status' => $statusLabelEn,
            ], 'en'),
        ];

        $this->data['type'] = 'order';
        $this->data['model_id'] = (string) $order->id;
        $this->data['status'] = $statusEnum->value;
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
            ->actions([
                \Filament\Notifications\Actions\Action::make('view')
                    ->label(__('dashboard.view'))
                    ->url(\App\Filament\Resources\OrderResource::getUrl('view', ['record' => $this->order])),
            ])
            ->data(['type' => 'order', 'model_id' => $this->order->id])
            ->getDatabaseMessage();
    }

    public function toFcm($notifiable): void
    {
        $url = \App\Filament\Resources\OrderResource::getUrl('view', ['record' => $this->order]);

        FCMAction::new($notifiable)
            ->withData($this->data)
            ->withTitle($this->data['title'][app()->getLocale()] ?? '')
            ->withBody($this->data['body'][app()->getLocale()] ?? '')
            ->withClickAction($url)
            ->sendMessage('tokens');
    }

    private function resolveStatus(): OrderStatusEnum
    {
        if ($this->status instanceof OrderStatusEnum) {
            return $this->status;
        }

        if (is_string($this->status) && $this->status !== '') {
            if ($this->status === 'expired') {
                return OrderStatusEnum::Completed;
            }

            return OrderStatusEnum::tryFrom($this->status) ?? $this->order->status;
        }

        return $this->order->status;
    }

    private function statusLabel(OrderStatusEnum $status, string $locale): string
    {
        return __('dashboard.status_'.$status->value, [], $locale);
    }
}
