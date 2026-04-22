<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'maintenance_id' => 'required|exists:maintenances,id,deleted_at,NULL,is_active,1',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'location_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'internal_note' => 'nullable|string|max:1000',
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|string',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:10240',
            'sounds' => 'nullable|array',
            'sounds.*' => 'nullable|mimes:mp3,wav,ogg,m4a,mp4,avi,mkv|max:10240',
        ];
    }
}
