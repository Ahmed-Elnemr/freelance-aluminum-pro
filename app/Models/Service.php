<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Service extends Model implements HasMedia
{
    use InteractsWithMedia, HasTranslations,  SoftDeletes;

    public array $translatable = ['name', 'content'];
    protected $fillable = [ 'category_service_id','category','name', 'content', 'price', 'discount', 'is_active'];
    //todo:relation
    public function categoryService(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CategoryService::class, 'category_service_id');
    }
    //todo: # end relation  #
}
