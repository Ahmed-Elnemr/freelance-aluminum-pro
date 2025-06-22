<?php

namespace App\Http\Requests\auth;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserNameRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:25', 'min:2', 'regex:/^[\p{Arabic}a-zA-Z\s]+$/u',],

        ];
    }

//    public function messages(): array
//    {
//        return [
//            'name.required' => __('api.validation.name.required'),
//            'name.string' => __('api.validation.name.string'),
//            'name.max' => __('api.validation.name.max'),
//            'name.min' => __('api.validation.name.min'),
//            'name.regex' => __('api.validation.name.format'),
//        ];
//    }
}
