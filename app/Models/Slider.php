<?php

namespace App\Models;

use App\Enum\SliderTypeEnum;
use App\Traits\HasActiveScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Slider extends Model implements HasMedia
{
    use InteractsWithMedia, HasTranslations, SoftDeletes, HasActiveScope;

    public array $translatable = ['name', 'content'];
    protected $fillable = [
        'name',
        'content',
        'type',
        'is_active'
    ];

    protected $casts = [
        'type' =>SliderTypeEnum::class,
    ];

}
