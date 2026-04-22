<?php

namespace App\Enum;

enum OrderStatusEnum: string
{
    case New = 'new';
    case Approved = 'approved';
    case Cancelled = 'cancelled';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::New => __('dashboard.status_new'),
            self::Approved => __('dashboard.status_approved'),
            self::Cancelled => __('dashboard.status_cancelled'),
            self::Completed => __('dashboard.status_completed'),
        };
    }

    public static function options(): array
    {
        return [
            self::New->value => self::New->label(),
            self::Approved->value => self::Approved->label(),
            self::Cancelled->value => self::Cancelled->label(),
            self::Completed->value => self::Completed->label(),
        ];
    }

    public function color(): string
    {
        return match ($this) {
            self::New => 'info',
            self::Approved => 'success',
            self::Cancelled => 'danger',
            self::Completed => 'gray',
        };
    }
}
