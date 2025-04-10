<?php

namespace App\Enum;

enum UserTypeEnum :string
{
    case CLIENT = 'client';
    case ADMIN = 'admin';


    public function label(): string
    {
        return match ($this) {
            self::CLIENT => __('dashboard.client'),
            self::ADMIN => __('dashboard.admin'),

        };
    }

    public static function options(): array
    {
        return [
            self::CLIENT->value => self::CLIENT->label(),
            self::ADMIN->value => self::ADMIN->label(),
        ];
    }
}
