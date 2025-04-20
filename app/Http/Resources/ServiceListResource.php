<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceListResource extends JsonResource
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
            'name' => (string)$this->name,
            'content' => (string)$this->content,
            'final_price' =>(double) $this->final_price,
            'is_favourite' => $this->isFavorited(),
            'base_image' => getDefaultImageUrl($this->base_image)
        ];
    }
}
