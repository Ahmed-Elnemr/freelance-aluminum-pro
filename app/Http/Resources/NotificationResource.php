<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $is_read = (bool) $this->read_at ;
        return [
            'id' => $this->id,
            'data' => new NotificationDataResource($this->data),
            'is_read' => $is_read,
            'created_at' => $this->created_at->format('h:i a').' '.$this->created_at->diffForHumans(),
        ];
    }
}
