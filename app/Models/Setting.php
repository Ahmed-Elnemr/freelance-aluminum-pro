<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Setting extends Model implements HasMedia
{
    use InteractsWithMedia ,HasTranslations;
    public array $translatable = ['name'];

    protected $fillable = ['name','key', 'value',  'type'];

    protected $hidden = ['created_at', 'updated_at'];
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('settings')->singleFile(); // Single image
    }

}
