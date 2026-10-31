<?php

namespace App\Notifications;

use App\Actions\FCMAction;
use App\Models\User;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class NewMessageFromAdminNotification extends Notification
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $data;

    public function __construct(public User $sender, public ?string $preview = null)
    {
        $previewText = $preview ? Str::limit(strip_tags($preview), 100) : '';

        $this->data['title'] = [
            'ar' => __('notification_new_admin_message_title', [], 'ar'),
            'en' => __('notification_new_admin_message_title', [], 'en'),
        ];

        $this->data['body'] = [
            'ar' => __('notification_new_admin_message_body', [], 'ar')
                .($previewText !== '' ? ' — '.$previewText : ''),
            'en' => __('notification_new_admin_message_body', [], 'en')
                .($previewText !== '' ? ' — '.$previewText : ''),
        ];

        $this->data['type'] = 'chat';
        $this->data['model_id'] = $sender->id;
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
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->data;
    }

    public function toFcm(object $notifiable): void
    {
        $locale = app()->getLocale();

        FCMAction::new($notifiable)
            ->withData([
                'type' => 'chat',
                'model_id' => (string) $this->sender->id,
            ])
            ->withTitle($this->data['title'][$locale] ?? $this->data['title']['ar'])
            ->withBody($this->data['body'][$locale] ?? $this->data['body']['ar'])
            ->sendMessage('tokens');
    }
}
