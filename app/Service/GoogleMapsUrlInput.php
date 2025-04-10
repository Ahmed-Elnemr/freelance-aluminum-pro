<?php

namespace App\Service;

use Filament\Forms\Components\TextInput;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Filament\Forms\Set;

class GoogleMapsUrlInput extends TextInput
{
    protected string $locationDataField;

    public function locationDataField(string $field): static
    {
        $this->locationDataField = $field;

        return $this;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateUpdated(function ($state, Set $set) {
            if (empty($state)) {
                return;
            }

            $locationData = $this->extractLocationData($state);
            if ($locationData) {
                $set($this->locationDataField, $locationData);
            }
        });
    }

    protected function extractLocationData(string $url): ?array
    {
        try {
            $client = new Client(['allow_redirects' => false]);
            $response = $client->get($url);
            $finalUrl = $response->getHeader('Location')[0] ?? $url;

            if (preg_match('/@([-0-9.]+),([-0-9.]+)/', $finalUrl, $matches)) {
                $locationName = $this->extractLocationName($finalUrl);

                return [
                    'latitude' => $matches[1],
                    'longitude' => $matches[2],
                    'location_name' => $locationName ?: "Location at {$matches[1]}, {$matches[2]}",
                    'map_url' => $url
                ];
            }

            return null;
        } catch (GuzzleException $e) {
            return null;
        }
    }

    private function extractLocationName(string $url): string
    {
        // استخراج اسم المكان من الرابط
        if (preg_match('/place\/([^\/+]+)/', $url, $matches)) {
            return urldecode(str_replace('+', ' ', $matches[1]));
        }

        // أو من جزء البيانات في الرابط
        if (preg_match('/data=([^!]+)/', $url, $dataMatches)) {
            parse_str(urldecode($dataMatches[1]), $data);
            return $data['name'] ?? '';
        }

        return '';
    }


}
