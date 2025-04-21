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
        'location_data',
        'description',
        'status',
        'is_active',
    ];

    protected $casts = [
        'status' => OrderStatusEnum::class,
        'location_data' => 'array',
    ];

    //todo: accessor


    public function getServiceTypeNameAttribute(): ?string
    {
        return $this->serviceType?->getTranslation('name', app()->getLocale());
    }

//todo:api method
    protected function getAddressName(float $latitude, float $longitude): string
    {
        $apiKey = config('services.google_maps.api_key');

        if (!$apiKey) {
            return "Location: {$latitude}, {$longitude}";
        }

        try {
            $client = new Client();
            $response = $client->get(
                "https://maps.googleapis.com/maps/api/geocode/json?latlng={$latitude},{$longitude}&key={$apiKey}"
            );
            $data = json_decode($response->getBody(), true);

            return $data['results'][0]['formatted_address'] ?? "Location: {$latitude}, {$longitude}";
        } catch (GuzzleException $e) {
            return "Location: {$latitude}, {$longitude}";
        }
    }
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
