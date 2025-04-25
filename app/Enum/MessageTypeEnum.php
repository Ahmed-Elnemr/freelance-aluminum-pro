<?php

namespace App\Enum;

enum MessageTypeEnum :string
{
    case TEXT = 'text';
    case FILE = 'file';
    case MULTIPLE = 'multiple';


    public function label(): string
    {
        return match ($this) {
            self::TEXT => __('text'),
            self::FILE => __('file'),
            self::MULTIPLE => __('multiple'),

        };
    }

    public static function options(): array
    {
        return [
            self::TEXT->value => self::TEXT->label(),
            self::FILE->value => self::FILE->label(),
            self::MULTIPLE->value => self::MULTIPLE->label(),
        ];
    }
}
