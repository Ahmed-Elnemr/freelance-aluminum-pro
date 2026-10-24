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
            'title' => $this->localizedTitle(),
            'type' => $this->resolvedType()->value,
            'url' => (string) $url,
            'mime_type' => (string) ($media?->mime_type ?? ''),
            'thumbnail' => $this->isImage() ? (string) $url : null,
        ];
    }
}
