<?php

namespace App\Enum;

enum DayOfWeekEnum: string
{
    case Saturday = 'saturday';
    case Sunday = 'sunday';
    case Monday = 'monday';
    case Tuesday = 'tuesday';
    case Wednesday = 'wednesday';
    case Thursday = 'thursday';
    case Friday = 'friday';

    public function label(): string
    {
        return match ($this) {
            self::Saturday => __('dashboard.saturday'),
            self::Sunday => __('dashboard.sunday'),
            self::Monday => __('dashboard.monday'),
            self::Tuesday => __('dashboard.tuesday'),
            self::Wednesday => __('dashboard.wednesday'),
            self::Thursday => __('dashboard.thursday'),
            self::Friday => __('dashboard.friday'),
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn (self $day) => [$day->value => $day->label()]
        )->toArray();
    }

    /**
     * Get ordered days starting from Saturday.
     *
     * @return array<int, self>
     */
    public static function ordered(): array
    {
        return [
            self::Saturday,
            self::Sunday,
            self::Monday,
            self::Tuesday,
            self::Wednesday,
            self::Thursday,
            self::Friday,
        ];
    }
}
