<?php

namespace App\Http\Resources\user;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int)$this->id,
//            'image' => (string)$this->image,
            'name' => (string)$this->name,
            'mobile' => (string)$this->mobile,
//            'email' => (string)$this->email,
            'type' => (string)$this->type,
            'access_token' => (string)$this->access_token,
        ];
    }
}
