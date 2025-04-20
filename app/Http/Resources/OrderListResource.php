<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locationData = is_string($this->location_data)
            ? json_decode($this->location_data, true)
            : $this->location_data;
        $locationName = $locationData['location_name'] ?? '';
        return [
            'id' => $this->id,
            'user_name' =>(string) $this->user?->name,
            'location' =>(string) $locationName,
            'description' =>(string) $this->description,
            'status' =>(string) $this->status->value,
            'status_label' =>(string) $this->status->label(),
            'created' => (string) $this->created_at?->format('d-m-Y'),
        ];
    }
}
