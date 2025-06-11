<?php

namespace App\Models;

use App\Enum\OrderStatusEnum;
use App\Traits\HasActiveScope;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;
use GuzzleHttp\Client;
use Illuminate\Support\Str;

class Order extends Model implements HasMedia
{

    use InteractsWithMedia, HasTranslations, SoftDeletes, HasActiveScope;

    public array $translatable = ['description'];
    protected $fillable = [
        'service_id',
        'user_id',
        'latitude',
        'longitude',
        'location_name',
        'description',
        'internal_note',
        'status',
        'is_active',
    ];

    protected $casts = [
        'status' => OrderStatusEnum::class,
    ];

    //todo: accessor


    public function getServiceTypeNameAttribute(): ?string
    {
        return $this->serviceType?->getTranslation('name', app()->getLocale());
    }


    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('media')->useDisk('public');
    }
//todo:api method end #

    //todo:api method end #


    //todo:relation
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function service(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    //todo: # end relation  #
}
