<?php

namespace App\Console\Commands;

use App\Enum\OrderStatusEnum;
use App\Enum\UserTypeEnum;
use App\Models\Order;
use App\Models\User;
use App\Notifications\AdminOrderFinishedNotification;
use App\Notifications\OrderReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

class ProcessOrders extends Command
{
    protected $signature = 'app:process-orders';

    protected $description = 'Handle order reminders and finish notifications';

    public function handle()
    {
        $now = Carbon::now();
        $today = $now->format('Y-m-d');
        $currentTime = $now->format('H:i');

        // 1. Reminders (30 mins before start)
        $reminderTime = $now->copy()->addMinutes(30)->format('H:i');
        $ordersToRemind = Order::where('scheduled_date', $today)
            ->where('scheduled_time', '<=', $reminderTime.':00')
            ->where('status', OrderStatusEnum::Approved)
            ->where('reminder_sent', false)
            ->get();

        foreach ($ordersToRemind as $order) {
            $order->user->notify(new OrderReminderNotification($order));
            $order->update(['reminder_sent' => true]);
            $this->info("Reminder sent for order #{$order->id}");
        }

        // 2. Finish Notifications (when end_time is reached)
        $ordersFinished = Order::where('scheduled_date', $today)
            ->where('end_time', '<=', $currentTime.':00')
            ->where('status', OrderStatusEnum::Approved)
            ->where('finish_notified', false)
            ->get();

        if ($ordersFinished->count() > 0) {
            $admins = User::where('type', UserTypeEnum::ADMIN->value)->active()->get();
            foreach ($ordersFinished as $order) {
                Notification::send($admins, new AdminOrderFinishedNotification($order));
                $order->update(['finish_notified' => true]);
                $this->info("Finish notification sent for order #{$order->id}");
            }
        }
    }
}
