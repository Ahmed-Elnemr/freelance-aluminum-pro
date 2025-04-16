<?php

namespace App\Enum;

enum SliderTypeEnum :string
{
    case INTRODUCTION = 'introduction';
    case INTERNAL = 'internal';


    public function label(): string
    {
        return match ($this) {
            self::INTRODUCTION => __('dashboard.introduction'),
            self::INTERNAL => __('dashboard.internal'),

        };
    }

    public static function options(): array
    {
        return [
            self::INTRODUCTION->value => self::INTRODUCTION->label(),
            self::INTERNAL->value => self::INTERNAL->label(),
        ];
    }
}
