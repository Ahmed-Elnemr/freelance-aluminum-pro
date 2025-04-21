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
        $locationDataRaw = $this->location_data;

        if (is_array($locationDataRaw)) {
            $locationData = $locationDataRaw;
        } elseif (is_string($locationDataRaw)) {
            $locationData = json_decode($locationDataRaw, true);
        } else {
            $locationData = [];
        }

        $locationName = $locationData['location_name'] ?? '';
        return [
            'id' => $this->id,
            'user_name'=>(string) $this->user?->name,
            'location' =>(string) $locationName,
            'category_type' =>(string) $this->service?->category?->value,
            'category_label' =>(string) $this->service?->category?->label(),
            'price' =>(double) $this->service->price,
            'final_price' =>(double) $this->service->final_price,
            'description' =>(string) $this->description,
            'status' =>(string) $this->status->value,
            'status_label' =>(string) $this->status->label(),
            'created' => (string) $this->created_at?->format('d-m-Y'),
        ];
    }
}
