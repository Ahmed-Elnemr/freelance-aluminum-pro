<?php

namespace App\Models;

use App\Enum\CategoryEnum;
use App\Traits\HasActiveScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Service extends Model implements HasMedia
{
    use InteractsWithMedia, HasTranslations, SoftDeletes, HasActiveScope;

    public array $translatable = ['name', 'content'];
    protected $fillable = [
        'category_service_id',
        'category',
        'name',
        'content',
        'price',
        'final_price',
        'is_active'
    ];

    protected $casts = [
        'category' => CategoryEnum::class,
    ];


    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('services');
    }
    //todo:scope
    public function similar()
    {
        return self::active()
            ->where('id', '!=', $this->id)
            ->where('category_service_id', $this->category_service_id)
            ->where('category', $this->category)
            ->latest()
            ->get();
    }

    public function averageRating(): float
    {
        return round($this->ratings()->avg('rating'), 1);
    }

    //todo:relation
    public function categoryService(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CategoryService::class, 'category_service_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);


    }

    public function ratings(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Rating::class, 'rateable');
    }


    //todo: # end relation  #
}
