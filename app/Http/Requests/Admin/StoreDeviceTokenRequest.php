<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeviceTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'device_token' => ['required', 'string', 'max:512'],
            'device_type' => ['required', 'string', 'in:web'],
            'uuid' => ['required', 'string', 'max:100'],
        ];
    }
}
