<?php

namespace App\Models;

use App\Enum\DayOfWeekEnum;
use Illuminate\Database\Eloquent\Model;

class WorkingDaySetting extends Model
{
    protected $fillable = [
        'day',
        'start_time',
        'end_time',
        'is_active',
    ];

    protected $casts = [
        'day' => DayOfWeekEnum::class,
        'is_active' => 'boolean',
    ];

    /**
     * Generate all 30-minute slots for this day between start_time and end_time.
     *
     * @return array<int, array{time: string, period: string}>
     */
    public function generateSlots(): array
    {
        $slots = [];
        $current = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);

        while ($current < $end) {
            $slots[] = [
                'time' => $current->format('H:i'),
                'period' => $current->format('A'), // AM or PM
            ];
            $current->addMinutes(30);
        }

        return $slots;
    }
}
