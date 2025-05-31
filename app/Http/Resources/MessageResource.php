<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
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
            'message' =>(string) $this->message,
            'type' =>(string) $this->type,
            'seen_at' =>(string) $this->seen_at,
            'created_at' => $this->created_at,
            'sender' => [
                'id' => $this->sender->id,
            ],
            'attachments' => AttachmentResource::collection($this->attachments),
        ];
    }
}
