<?php

namespace App\Http\Requests\api;

use Illuminate\Foundation\Http\FormRequest;

class QuickOrderRequest extends FormRequest
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
            'message' => ['nullable', 'string', 'max:5000'],
            'sound' => ['nullable', 'array'],
            'sound.*' => ['file', 'mimes:mp3,wav,ogg,mpga,audio/mpeg,audio/mp4', 'max:10240'], // Max 10MB
        ];
    }

    public function messages(): array
    {
        return [
            'message.string' => __('validation.string', ['attribute' => __('dashboard.message')]),
            'sound.array' => __('validation.array', ['attribute' => __('dashboard.sounds')]),
            'sound.*.file' => __('validation.file', ['attribute' => __('dashboard.sounds')]),
            'sound.*.mimes' => __('validation.mimes', ['attribute' => __('dashboard.sounds'), 'values' => 'mp3, wav, ogg']),
        ];
    }
}
