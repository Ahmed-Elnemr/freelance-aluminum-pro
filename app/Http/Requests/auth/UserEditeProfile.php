<?php

namespace App\Http\Requests\auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserEditeProfile extends FormRequest
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
                'sometimes',
                'required',
                'string',
                'max:255',
                'min:2',
            ],
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore(auth()->id())->whereNull('deleted_at'),
            ],
            'mobile' => [
                'sometimes',
                'required',
                'string',
                'regex:/^05\d{8}$/',
                'max:15',
                Rule::unique('users', 'mobile')->ignore(auth()->id())->whereNull('deleted_at'),
            ],
            'current_password' => [
                'required_with:password',
                'string',
            ],
            'password' => [
                'sometimes',
                'required',
                'string',
                'min:4',
                'confirmed',
            ],
            'password_confirmation' => [
                'required_with:password',
                'string',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('validation.attributes.name'),
            'email' => __('validation.attributes.email'),
            'mobile' => __('validation.attributes.mobile'),
            'current_password' => __('validation.attributes.current_password'),
            'password' => __('validation.attributes.password'),
            'password_confirmation' => __('validation.attributes.password_confirmation'),
        ];
    }

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
            
            'current_password.required_with' => __('validation.required_with', ['attribute' => __('validation.attributes.current_password'), 'values' => __('validation.attributes.password')]),
            
            'password.required' => __('validation.required', ['attribute' => __('validation.attributes.password')]),
            'password.string' => __('validation.string', ['attribute' => __('validation.attributes.password')]),
            'password.min' => __('validation.min.string', ['attribute' => __('validation.attributes.password'), 'min' => 4]),
            'password.confirmed' => __('validation.confirmed', ['attribute' => __('validation.attributes.password')]),
            
            'password_confirmation.required_with' => __('validation.required_with', ['attribute' => __('validation.attributes.password_confirmation'), 'values' => __('validation.attributes.password')]),
        ];
    }
}
