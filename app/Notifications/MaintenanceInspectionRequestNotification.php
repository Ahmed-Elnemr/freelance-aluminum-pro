<?php

namespace App\Notifications;

use App\Actions\FCMAction;
use App\Models\MaintenanceInspection;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MaintenanceInspectionRequestNotification extends Notification
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    public function __construct(public MaintenanceInspection $inspection)
    {
        $this->data['title'] = [
            'ar' => 'طلب معاينة جديد',
            'en' => 'New Inspection Request',
        ];

        $this->data['body'] = [
            'ar' => 'لقد طلب '.$inspection->user?->name.' معاينة لصيانة '.$inspection->maintenance?->getTranslation('name', 'ar'),
            'en' => $inspection->user?->name.' requested an inspection for '.$inspection->maintenance?->getTranslation('name', 'en'),
        ];

        $this->data['type'] = 'inspection';
        $this->data['model_id'] = $inspection->id;
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
                    ->url(\App\Filament\Resources\MaintenanceInspectionResource::getUrl('view', ['record' => $this->inspection])),
            ])
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
