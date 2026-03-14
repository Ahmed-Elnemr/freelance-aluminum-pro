<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaintenanceResource extends JsonResource
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
            'name' => (string) $this->name,
            'content' => (string) $this->content,
            'price' => (float) $this->price,
            'final_price' => (float) $this->final_price,
            'rate' => round($this->ratings_avg_rating ?? 0, 1),
            'ratings_count' => $this->ratings_count ?? 0,
            'my_rating' => (float) $this->my_rating,
            'is_favourite' => $this->isFavorited(),
            'base_image' => optional($this->getFirstMedia('maintenances'))->getUrl(),
            'images' => $this->getMedia('maintenances')->map(fn ($media) => $media->getUrl()),
        ];
    }
}
