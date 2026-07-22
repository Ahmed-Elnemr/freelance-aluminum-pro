<?php

namespace App\Listeners;

use Filament\Notifications\Events\DatabaseNotificationsSent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Events\NotificationSent;

class DispatchFilamentDatabaseNotificationsSent
{
    public function handle(NotificationSent $event): void
    {
        if ($event->channel !== 'database') {
            return;
        }

        if (! $event->notifiable instanceof Model) {
            return;
        }

        event(new DatabaseNotificationsSent($event->notifiable));
    }
}
