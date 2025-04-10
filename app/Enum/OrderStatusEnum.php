<?php

namespace App\Enum;

enum OrderStatusEnum :string
{
    case CURRENT = 'current';
    case EXPIRED = 'expired';


    public function label(): string
    {
        return match ($this) {
            self::CURRENT => __('dashboard.current'),
            self::EXPIRED => __('dashboard.expired'),

        };
    }

    public static function options(): array
    {
        return [
            self::CURRENT->value => self::CURRENT->label(),
            self::EXPIRED->value => self::EXPIRED->label(),
        ];
    }
}
