<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MainServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'name'      => (string)$this->name,
            'content'   =>(string) $this->content,
            'images'    => $this->getMedia('main_services')->map(function ($media) {
                return $media->getFullUrl();
            }),
        ];
    }
}
