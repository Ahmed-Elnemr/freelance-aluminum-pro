<?php

namespace App\Http\Resources\chat;

use App\Http\Resources\user\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id ?? null,  // تحقق من وجود id
            'client' => new UserResource($this->whenLoaded('client')),
            'admin' => new UserResource($this->whenLoaded('admin')),
            'last_message_at' => $this->last_message_at ? $this->last_message_at->format('Y-m-d h:i A') : null,  // تنسيق التاريخ والوقت
            'messages' => MessageResource::collection($this->whenLoaded('messages')),
            'last_message' => $this->messages->first() ? new MessageResource($this->messages->first()) : null,
            'unread_count' => $this->messages()->where('receiver_id', auth()->id())->whereNull('seen_at')->count(),
        ];
    }
}
