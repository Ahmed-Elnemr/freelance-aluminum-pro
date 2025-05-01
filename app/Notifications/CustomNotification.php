<?php

namespace App\Notifications;

use App\Actions\FCMAction;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;

class CustomNotification extends Notification
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $data;

    public function __construct(array $title, array $body)
    {
        $this->data = [
            'title' => $title,
            'body' => $body,
            'type' => 'custom_notification',
        ];
    }

    public function via(object $notifiable): array
    {
        return ['database',FcmChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('Notification preview')
            ->action('View', url('/'))
            ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        return $this->data;
    }

    public function toFcm($notifiable)
    {
//        dd($notifiable->fcm_token);
//        if (!$notifiable->fcm_token) {
//            return;
//        }

        FCMAction::new($notifiable)
            ->withData($this->data)
            ->withTitle($this->data['title'][app()->getLocale()] ?? '')
            ->withBody($this->data['body'][app()->getLocale()] ?? '')
            ->sendMessage('tokens');
    }
}
