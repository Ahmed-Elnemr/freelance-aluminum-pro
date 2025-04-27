<?php

namespace App\Filament\Resources\NotificationResource\Pages;

use App\Filament\Resources\NotificationResource;
use App\Models\User;
use App\Notifications\CustomNotification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Arr;

class CreateNotification extends CreateRecord
{
    protected static string $resource = NotificationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // منع التنفيذ المزدوج
        static $executed = false;
        if ($executed) {
            return [];
        }
        $executed = true;

        // تنظيف مصفوفة المستخدمين المحددين
        $selectedUsers = Arr::whereNotNull($data['users'] ?? []);

        if ($data['send_to_all'] ?? false) {
            // إرسال لجميع المستخدمين النشطين
            $users = User::query()
                ->where('is_active', 1)
                ->where('status', 1)
                ->where('type', 'client')
                ->whereNull('deleted_at')
                ->cursor();
        } else {
            // إرسال للمستخدمين المحددين فقط بعد التنظيف
            $users = User::whereIn('id', $selectedUsers)->get();
        }

        $count = 0;
        foreach ($users as $user) {
            try {
                $user->notify(new CustomNotification(
                    title: [
                        'en' => $data['title_en'],
                        'ar' => $data['title_ar'],
                    ],
                    body: [
                        'en' => $data['body_en'],
                        'ar' => $data['body_ar'],
                    ]
                ));
                $count++;
            } catch (\Exception $e) {
                // تسجيل الخطأ دون إيقاف العملية
                logger()->error("فشل إرسال إشعار للمستخدم {$user->id}: " . $e->getMessage());
            }
        }

        // إضافة رسالة توضح عدد الإشعارات المرسلة
        \Filament\Notifications\Notification::make()
            ->title("تم إرسال {$count} إشعار بنجاح")
            ->success()
            ->send();
        // منع Filament من محاولة حفظ السجل
        $this->redirect($this->getRedirectUrl());
        $this->halt();
        return [];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
