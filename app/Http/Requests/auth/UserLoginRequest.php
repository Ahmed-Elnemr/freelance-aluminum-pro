<?php

namespace App\Http\Requests\auth;


class UserLoginRequest extends FormRequest
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
            'login' => [
                'required',
                'string',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
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
            'login' => __('validation.attributes.login'),
            'password' => __('validation.attributes.password'),
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
            'login.required' => __('validation.required', ['attribute' => __('validation.attributes.login')]),
            'login.string' => __('validation.string', ['attribute' => __('validation.attributes.login')]),

            'password.required' => __('validation.required', ['attribute' => __('validation.attributes.password')]),
            'password.string' => __('validation.string', ['attribute' => __('validation.attributes.password')]),
            'password.min' => __('validation.min.string', ['attribute' => __('validation.attributes.password'), 'min' => 8]),

            'uuid.required' => __('validation.required', ['attribute' => __('validation.attributes.uuid')]),
            'uuid.string' => __('validation.string', ['attribute' => __('validation.attributes.uuid')]),

            'device_token.required' => __('validation.required', ['attribute' => __('validation.attributes.device_token')]),

            'device_type.required' => __('validation.required', ['attribute' => __('validation.attributes.device_type')]),
            'device_type.string' => __('validation.string', ['attribute' => __('validation.attributes.device_type')]),
        ];
    }
}
