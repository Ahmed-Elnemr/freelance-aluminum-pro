<?php

namespace App\Notifications;

use App\Actions\FCMAction;
use App\Models\Order;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OrderCreatedNotification extends Notification
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Order $order)
    {
        $this->data['title'] = [
            'ar' => __('notification_new_order_title', [], 'ar'),
            'en' => __('notification_new_order_title', [], 'en'),
        ];

        $this->data['body'] = [
            'ar' => __('notification_new_order_body', ['service_name' => $order->service?->getTranslation('name', 'ar')], 'ar'),
            'en' => __('notification_new_order_body', ['service_name' => $order->service?->getTranslation('name', 'en')], 'en'),
        ];

        $this->data['type'] = 'order';
        $this->data['model_id'] = $order->id;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', FcmChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->data;
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
