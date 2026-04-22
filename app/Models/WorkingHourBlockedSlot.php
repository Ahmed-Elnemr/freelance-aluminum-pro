<?php

namespace App\Models;

use App\Enum\DayOfWeekEnum;
use Illuminate\Database\Eloquent\Model;

class WorkingHourBlockedSlot extends Model
{
    protected $fillable = [
        'day',
        'slot_time',
    ];

    protected $casts = [
        'day' => DayOfWeekEnum::class,
    ];
}
