<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
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
            'price' => (double)$this->price,
            'final_price' => (double)$this->final_price,
            'rate'=>(float)3,
            'is_favourite'=>(bool)true,
            'images' => [
                getDefaultImageUrl($this->image),
                getDefaultImageUrl($this->image),
                getDefaultImageUrl($this->image),
                getDefaultImageUrl($this->image),
            ]
        ];
    }
}
