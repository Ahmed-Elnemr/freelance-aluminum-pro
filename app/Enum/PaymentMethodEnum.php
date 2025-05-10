<?php

namespace App\Enum;

enum    PaymentMethodEnum: int
{
    case moyasar = 1;


    public function label(): string
    {
        return match ($this) {
            self::moyasar => __('moyasar'),
        };
    }


}
