<?php

namespace App\Http\Requests\auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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
            'otp' => 'required|numeric',
            'password' => 'required|string|min:4|confirmed',
        ];
    }

    public function attributes()
    {
        return [
            'email' => __('validation.attributes.email'),
            'otp' => __('validation.attributes.otp'),
            'password' => __('validation.attributes.password'),
        ];
    }

    public function messages(): array
    {
        return [
            'email.exists' => __('auth.user_not_found'),
            'password.confirmed' => __('validation.confirmed', ['attribute' => __('validation.attributes.password')]),
        ];
    }
}
