<?php

namespace App\Http\Requests\auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:255',
                'min:2',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'mobile' => [
                'required',
                'string',
                'regex:/^05\d{8}$/',
                'max:15',
                'unique:users,mobile',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'password_confirmation' => [
                'required',
                'string',
            ],
            'uuid' => 'required|string',
            'device_token' => 'required',
            'device_type' => 'required|string',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => __('validation.attributes.name'),
            'email' => __('validation.attributes.email'),
            'mobile' => __('validation.attributes.mobile'),
            'password' => __('validation.attributes.password'),
            'password_confirmation' => __('validation.attributes.password_confirmation'),
            'uuid' => __('validation.attributes.uuid'),
            'device_token' => __('validation.attributes.device_token'),
            'device_type' => __('validation.attributes.device_type'),
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => __('validation.required', ['attribute' => __('validation.attributes.name')]),
            'name.string' => __('validation.string', ['attribute' => __('validation.attributes.name')]),
            'name.max' => __('validation.max.string', ['attribute' => __('validation.attributes.name'), 'max' => 255]),
            'name.min' => __('validation.min.string', ['attribute' => __('validation.attributes.name'), 'min' => 2]),
            
            'email.required' => __('validation.required', ['attribute' => __('validation.attributes.email')]),
            'email.email' => __('validation.email', ['attribute' => __('validation.attributes.email')]),
            'email.max' => __('validation.max.string', ['attribute' => __('validation.attributes.email'), 'max' => 255]),
            'email.unique' => __('validation.unique', ['attribute' => __('validation.attributes.email')]),
            
            'mobile.required' => __('validation.required', ['attribute' => __('validation.attributes.mobile')]),
            'mobile.string' => __('validation.string', ['attribute' => __('validation.attributes.mobile')]),
            'mobile.regex' => __('validation.mobile_format'),
            'mobile.max' => __('validation.max.string', ['attribute' => __('validation.attributes.mobile'), 'max' => 15]),
            'mobile.unique' => __('validation.unique', ['attribute' => __('validation.attributes.mobile')]),
            
            'password.required' => __('validation.required', ['attribute' => __('validation.attributes.password')]),
            'password.string' => __('validation.string', ['attribute' => __('validation.attributes.password')]),
            'password.min' => __('validation.min.string', ['attribute' => __('validation.attributes.password'), 'min' => 8]),
            'password.confirmed' => __('validation.confirmed', ['attribute' => __('validation.attributes.password')]),
            
            'password_confirmation.required' => __('validation.required', ['attribute' => __('validation.attributes.password_confirmation')]),
            
            'uuid.required' => __('validation.required', ['attribute' => __('validation.attributes.uuid')]),
            'device_token.required' => __('validation.required', ['attribute' => __('validation.attributes.device_token')]),
            'device_type.required' => __('validation.required', ['attribute' => __('validation.attributes.device_type')]),
        ];
    }
}
