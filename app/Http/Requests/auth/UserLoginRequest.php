<?php

namespace App\Http\Requests\auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'mobile' => [
                'required',
                'regex:/^05\d{8}$/',
                'max:15',
            ],
            'uuid' => 'required|string',
            'device_token' => 'required',
            'device_type' => 'required|string',
        ];
    }


    public function messages(): array
    {
        return [
            'mobile_required' => __('validation.mobile_required'),
            'mobile.exists' => __('validation.mobile_exists'),
            'uuid.required' => __('validation.uuid_required'),
            'uuid.string' => __('validation.uuid_string'),
            'device_token.required' => __('validation.device_token_required'),
            'device_type.required' => __('validation.device_type_required'),
            'device_type.string' => __('validation.device_type_string'),
        ];
    }
}
