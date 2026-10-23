<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GalleryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $media = $this->getFirstMedia('gallery');
        $url = $media?->getUrl();

        return [
            'id' => (int) $this->id,
            'title' => (string) $this->title,
            'type' => (string) ($this->type?->value ?? 'image'),
            'url' => (string) $url,
            'mime_type' => (string) ($media?->mime_type ?? ''),
            'thumbnail' => $this->isImage() ? (string) $url : null,
        ];
    }
}
