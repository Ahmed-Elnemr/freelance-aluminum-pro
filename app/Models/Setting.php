<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Setting extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $fillable = ['key', 'value',  'type'];

    protected $hidden = ['created_at', 'updated_at'];
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('settings')->singleFile(); // Single image
    }

}
