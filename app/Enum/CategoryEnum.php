<?php

namespace App\Enum;

enum CategoryEnum :string
{
    case PRODUCTS = 'products';
    case MAINTENANCE = 'maintenance';


    public function label(): string
    {
        return match ($this) {
            self::PRODUCTS => __('dashboard.products'),
            self::MAINTENANCE => __('dashboard.maintenance'),

        };
    }

    public static function options(): array
    {
        return [
            self::PRODUCTS->value => self::PRODUCTS->label(),
            self::MAINTENANCE->value => self::MAINTENANCE->label(),
        ];
    }
}
