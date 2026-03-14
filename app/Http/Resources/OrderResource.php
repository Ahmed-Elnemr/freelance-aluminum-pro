<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_name' => (string) $this->user?->name,
            'service_name' => (string) $this->maintenance?->getTranslation('name', app()->getLocale()),
            'location' => (string) $this->location_name,
            'location_name' => (string) $this->location_name,
            'latitude' => (float) $this->latitude,
            'longitude' => (float) $this->longitude,
            'maintenance_id' => (int) $this->maintenance?->id,
            'price' => (float) $this->maintenance?->price,
            'final_price' => (float) $this->maintenance?->final_price,
            'description' => (string) $this->description,
            'internal_note' => (string) $this->internal_note,
            'status' => (string) $this->status->value,
            'status_label' => (string) $this->status->label(),
            'date' => (string) $this->date?->format('Y-m-d'),
            'time' => (string) $this->time,
            'formatted_time' => (string) $this->formatted_time,
            'created' => (string) $this->created_at?->format('d-m-Y'),
            'media' => $this->getMedia('media')->map(function ($media) {
                return [
                    'url' => $media->getUrl(),
                    'type' => $media->mime_type,
                ];
            }),
            'sounds' => $this->getMedia('sounds')->map(function ($media) {
                return [
                    'url' => $media->getUrl(),
                    'type' => $media->mime_type,
                ];
            }),
        ];
    }
}
