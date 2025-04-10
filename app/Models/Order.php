<?php

namespace App\Models;

use App\Enum\OrderStatusEnum;
use App\Traits\HasActiveScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Order extends Model implements HasMedia
{

    use InteractsWithMedia, HasTranslations, SoftDeletes, HasActiveScope;

    public array $translatable = ['description'];
    protected $fillable = [
        'service_id',
        'user_id',
        'maintenance_type_id',
        'location_data',
        'description',
        'status',
        'is_active'
    ];

    protected $casts = [
        'status' => OrderStatusEnum::class,
    ];

    //todo:relation
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function service(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function maintenanceType(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MaintenanceType::class);
    }

    //todo: # end relation  #
}
