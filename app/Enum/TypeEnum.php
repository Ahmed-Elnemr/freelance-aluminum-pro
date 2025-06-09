<?php

namespace App\Enum;

enum TypeEnum :string
{
    case HOME = 'home';
    case SERVICES = 'services';


    public function label(): string
    {
        return match ($this) {
            self::HOME => __('home'),
            self::SERVICES => __('services'),

        };
    }

    public static function options(): array
    {
        return [
            self::HOME->value => self::HOME->label(),
            self::SERVICES->value => self::SERVICES->label(),
        ];
    }
}
