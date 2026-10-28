<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderRatingResource extends JsonResource
{
    /**
     * @return array{id: int, stars: int, comment: ?string, tags: list<string>, created_at: ?string}
     */
    public function toArray(Request $request): array
    {
        return $this->resource->toApiArray();
    }
}
