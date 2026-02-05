<?php

namespace App\Http\Requests\auth;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
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
            'email' => 'required|email|exists:users,email,type,client',
        ];
    }

    public function attributes()
    {
        return [
            'email' => __('validation.attributes.email'),
        ];
    }

    public function messages(): array
    {
        return [
            'email.exists' => __('auth.user_not_found'),
        ];
    }
}
