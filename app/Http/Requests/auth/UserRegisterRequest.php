<?php

namespace App\Http\Requests\auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRegisterRequest extends FormRequest
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
                Rule::unique('users', 'mobile')->whereNull('deleted_at')
            ],
            'name' => ['required', 'string', 'max:25', 'min:5', 'regex:/^[\p{Arabic}a-zA-Z\s]+$/u',],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        // Using trans() helper instead of __() for more explicit translation
        return [
            'mobile.required' => trans('api.validation.mobile.required'),
            'mobile.regex' => trans('api.validation.mobile.format'),
            'mobile.max' => trans('api.validation.mobile.max'),
            'mobile.unique' => trans('api.validation.mobile.unique'),

            'name.required' => trans('api.validation.name.required'),
            'name.string' => trans('api.validation.name.string'),
            'name.max' => trans('api.validation.name.max'),
            'name.min' => trans('api.validation.name.min'),
            'name.regex' => trans('api.validation.name.format'),
        ];
    }

    protected function prepareForValidation()
    {
        // For debugging
        logger('Current Locale: ' . app()->getLocale());
        logger('Translation test: ' . trans('Please enter your mobile number'));
    }
}
