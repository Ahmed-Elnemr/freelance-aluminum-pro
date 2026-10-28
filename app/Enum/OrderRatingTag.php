<?php

namespace App\Enum;

enum OrderRatingTag: string
{
    case OnTime = 'on_time';
    case Quality = 'quality';
    case Professional = 'professional';
    case Clean = 'clean';
    case FairPrice = 'fair_price';
    case Late = 'late';
    case PoorQuality = 'poor_quality';
    case Unprofessional = 'unprofessional';
    case Messy = 'messy';
    case Expensive = 'expensive';

    public function label(): string
    {
        return __('ratings.tags_list.'.$this->value);
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
