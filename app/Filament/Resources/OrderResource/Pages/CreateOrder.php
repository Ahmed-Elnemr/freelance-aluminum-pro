<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Http;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!empty($data['google_maps_url'])) {
            // توسيع الرابط المختصر
            $expandedUrl = $this->expandShortUrl($data['google_maps_url']);

            if ($expandedUrl) {
                preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $expandedUrl, $matches);

                if (count($matches) === 3) {
                    $latitude = (float) $matches[1];
                    $longitude = (float) $matches[2];

                    $data['latitude'] = $latitude;
                    $data['longitude'] = $longitude;

                    $data['location_name'] = $this->getAddressName($latitude, $longitude);
                }
            }
        }

        return $data;
    }

    protected function expandShortUrl(string $url): ?string
    {
        try {
            $response = Http::head($url);
            return $response->effectiveUri();
        } catch (\Exception $e) {
            return null;
        }
    }
    protected function getAddressName(float $latitude, float $longitude): string
    {
        $apiKey = config('services.google_maps.api_key');

        if (!$apiKey) {
            return "موقع تقريبي: خط العرض {$latitude}، خط الطول {$longitude}";
        }

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->get(
                "https://maps.googleapis.com/maps/api/geocode/json?latlng={$latitude},{$longitude}&key={$apiKey}"
            );
            $data = json_decode($response->getBody(), true);

            return $data['results'][0]['formatted_address'] ?? "موقع تقريبي: خط العرض {$latitude}، خط الطول {$longitude}";
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            return "موقع تقريبي: خط العرض {$latitude}، خط الطول {$longitude}";
        }
    }
}
