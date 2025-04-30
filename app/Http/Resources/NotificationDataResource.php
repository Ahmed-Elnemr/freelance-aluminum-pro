<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => (string) @$this['title'][app()->getLocale()],
            'message' => (string) @$this['body'][app()->getLocale()],
            'type' => @$this['type'] ?? '',
            'model_id' => (int) @$this['model_id'],
        ];
    }
}
