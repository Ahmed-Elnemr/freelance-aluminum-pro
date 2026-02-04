<?php

namespace App\Http\Requests\auth;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ];
    }

    public function attributes()
    {
        return [
            'current_password' => __('validation.attributes.current_password'),
            'password' => __('validation.attributes.password'),
        ];
    }
}
