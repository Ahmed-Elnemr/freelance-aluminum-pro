<?php

namespace App\Enum;

enum GalleryTypeEnum: string
{
    case Image = 'image';
    case Video = 'video';

    public function label(): string
    {
        return match ($this) {
            self::Image => __('dashboard.image'),
            self::Video => __('dashboard.video'),
        };
    }

    public static function options(): array
    {
        return [
            self::Image->value => self::Image->label(),
            self::Video->value => self::Video->label(),
        ];
    }
}
