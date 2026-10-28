<?php

namespace App\Notifications;

use App\Actions\FCMAction;
use App\Models\Order;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderCompletedNotification extends Notification
{
    use Queueable;

    /**
     * @var array{title: array{ar: string, en: string}, body: array{ar: string, en: string}, type: string, model_id: int}
     */
    protected array $data;

    public function __construct(public Order $order)
    {
        $this->data = [
            'title' => [
                'ar' => __('ratings.notification_title', [], 'ar'),
                'en' => __('ratings.notification_title', [], 'en'),
            ],
            'body' => [
                'ar' => __('ratings.notification_body', [], 'ar'),
                'en' => __('ratings.notification_body', [], 'en'),
            ],
            'type' => 'order_rating',
            'model_id' => $order->id,
        ];
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', FcmChannel::class];
    }

    /**
     * @return array{title: array{ar: string, en: string}, body: array{ar: string, en: string}, type: string, model_id: int}
     */
    public function toArray(object $notifiable): array
    {
        return $this->data;
    }

    /**
     * @return array{title: array{ar: string, en: string}, body: array{ar: string, en: string}, type: string, model_id: int}
     */
    public function toDatabase(object $notifiable): array
    {
        return $this->data;
    }

    public function toFcm(object $notifiable): void
    {
        $locale = app()->getLocale();

        FCMAction::new($notifiable)
            ->withData([
                'type' => 'order_rating',
                'model_id' => (string) $this->order->id,
            ])
            ->withTitle($this->data['title'][$locale] ?? $this->data['title']['ar'])
            ->withBody($this->data['body'][$locale] ?? $this->data['body']['ar'])
            ->sendMessage('tokens');
    }
}
