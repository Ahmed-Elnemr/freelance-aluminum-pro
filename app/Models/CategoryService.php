<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class CategoryService extends Model  implements HasMedia
{
    use InteractsWithMedia, HasTranslations,  SoftDeletes;

    public array $translatable = ['name'];
    protected $fillable = ['name', 'is_active'];

    //todo:relation
    public function services()
    {
        return $this->hasMany(Service::class, 'category_service_id');
    }
    //todo: # end relation  #


}
