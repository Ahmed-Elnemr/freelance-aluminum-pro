<?php

namespace App\Models;

use App\Enum\CategoryEnum;
use App\Traits\HasActiveScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Service extends Model implements HasMedia
{
    use InteractsWithMedia, HasTranslations,  SoftDeletes , HasActiveScope;

    public array $translatable = ['name', 'content'];
    protected $casts = [
        'category' =>CategoryEnum::class,
    ];


    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('services');
    }
    protected $fillable = [ 'category_service_id','category','name', 'content', 'price', 'discount', 'is_active'];
    //todo:relation
    public function categoryService(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CategoryService::class, 'category_service_id');
    }
    //todo: # end relation  #
}
