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
        $title = $this['title'] ?? '';
        if (is_array($title)) {
            $title = $title[app()->getLocale()] ?? $title['en'] ?? array_values($title)[0] ?? '';
        }

        $body = $this['body'] ?? $this['message'] ?? '';
        if (is_array($body)) {
            $body = $body[app()->getLocale()] ?? $body['en'] ?? array_values($body)[0] ?? '';
        }

        $type = $this['type'] ?? $this['data']['type'] ?? $this['viewData']['type'] ?? '';
        $modelId = $this['model_id'] ?? $this['data']['model_id'] ?? $this['viewData']['model_id'] ?? 0;

        return [
            'title' => (string) $title,
            'message' => (string) $body,
            'type' => (string) $type,
            'model_id' => (int) $modelId,
        ];
    }
}
