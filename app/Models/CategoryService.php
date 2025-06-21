<?php

namespace App\Models;

use App\Traits\HasActiveScope;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class CategoryService extends Model  implements HasMedia
{
    use InteractsWithMedia, HasTranslations,  SoftDeletes, HasActiveScope;

    public array $translatable = ['name'];
    protected $fillable = ['name', 'is_active'];

    //todo:relation
    public function services()
    {
        return $this->hasMany(Service::class, 'category_service_id');
    }
    //todo: # end relation  #

    //todo:filament
    protected static function booted(): void
    {
        static::deleting(function ($categorySerice) {
            if ($categorySerice->services()->exists()) {
                Notification::make()
                    ->title(__('This category cannot be deleted because it contains services.'))
                    ->danger()
                    ->send();
                return false;
            }
        });
    }
}
