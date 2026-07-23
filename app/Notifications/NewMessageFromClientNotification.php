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

class NewMessageFromClientNotification extends Notification
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $data;

    public function __construct(public User $sender, public ?string $preview = null)
    {
        $previewText = $preview ? Str::limit(strip_tags($preview), 100) : '';

        $this->data['title'] = [
            'ar' => __('notification_new_client_message_title', [], 'ar'),
            'en' => __('notification_new_client_message_title', [], 'en'),
        ];

        $this->data['body'] = [
            'ar' => __('notification_new_client_message_body', ['user_name' => $sender->name], 'ar')
                .($previewText !== '' ? ' — '.$previewText : ''),
            'en' => __('notification_new_client_message_body', ['user_name' => $sender->name], 'en')
                .($previewText !== '' ? ' — '.$previewText : ''),
        ];

        $this->data['type'] = 'chat';
        $this->data['model_id'] = (string) $sender->id;
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
                    ->label(__('dashboard.chat'))
                    ->url(\App\Filament\Pages\ChatPage::getUrl(['userId' => $this->sender->id])),
            ])
            ->data(['type' => 'chat', 'model_id' => $this->sender->id])
            ->getDatabaseMessage();
    }

    public function toFcm($notifiable): void
    {
        $url = \App\Filament\Pages\ChatPage::getUrl(['userId' => $this->sender->id]);

        FCMAction::new($notifiable)
            ->withData($this->data)
            ->withTitle($this->data['title'][app()->getLocale()] ?? '')
            ->withBody($this->data['body'][app()->getLocale()] ?? '')
            ->withClickAction($url)
            ->sendMessage('tokens');
    }
}
