<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\TranslationLoader\LanguageLine;

return new class extends Migration
{
    /**
     * @return array<string, array{ar: string, en: string}>
     */
    private function translations(): array
    {
        return [
            'maintenance' => [
                'ar' => 'الصيانة',
                'en' => 'Maintenance',
            ],
            'service_inspection' => [
                'ar' => 'معاينة خدمة',
                'en' => 'Service Inspection',
            ],
            'service_inspections' => [
                'ar' => 'معاينات الخدمات',
                'en' => 'Service Inspections',
            ],
            'inspected_at' => [
                'ar' => 'تاريخ المعاينة',
                'en' => 'Inspected At',
            ],
            'quick_order' => [
                'ar' => 'طلب سريع',
                'en' => 'Quick Order',
            ],
            'quick_orders' => [
                'ar' => 'الطلبات السريعة',
                'en' => 'Quick Orders',
            ],
            'message' => [
                'ar' => 'الرسالة',
                'en' => 'Message',
            ],
            'sounds' => [
                'ar' => 'التسجيلات الصوتية',
                'en' => 'Audio Recordings',
            ],
            'quick_order_created_successfully' => [
                'ar' => 'تم إرسال طلبك السريع بنجاح.',
                'en' => 'Your quick order has been sent successfully.',
            ],
            'mobile' => [
                'ar' => 'رقم الجوال',
                'en' => 'Mobile',
            ],
            'from' => [
                'ar' => 'من',
                'en' => 'From',
            ],
            'to' => [
                'ar' => 'إلى',
                'en' => 'To',
            ],
        ];
    }

    public function up(): void
    {
        foreach ($this->translations() as $key => $text) {
            LanguageLine::updateOrCreate(
                [
                    'group' => 'dashboard',
                    'key' => $key,
                ],
                [
                    'text' => $text,
                ]
            );
        }
    }

    public function down(): void
    {
        LanguageLine::query()
            ->where('group', 'dashboard')
            ->whereIn('key', array_keys($this->translations()))
            ->delete();
    }
};
