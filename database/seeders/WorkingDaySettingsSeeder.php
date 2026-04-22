<?php

namespace Database\Seeders;

use App\Models\WorkingDaySetting;
use Illuminate\Database\Seeder;

class WorkingDaySettingsSeeder extends Seeder
{
    public function run(): void
    {
        $days = [
            'saturday',
            'sunday',
            'monday',
            'tuesday',
            'wednesday',
            'thursday',
            'friday',
        ];

        foreach ($days as $day) {
            WorkingDaySetting::updateOrCreate(
                ['day' => $day],
                [
                    'start_time' => '09:00:00',
                    'end_time' => '21:00:00',
                    'is_active' => $day !== 'friday', // Friday off by default
                ]
            );
        }
    }
}
