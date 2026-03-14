<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaintenanceListResource extends JsonResource
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
            'final_price' => (float) $this->final_price,
            'is_favourite' => $this->isFavorited(),
            'base_image' => optional($this->getFirstMedia('maintenances'))->getUrl(),
        ];
    }
}
