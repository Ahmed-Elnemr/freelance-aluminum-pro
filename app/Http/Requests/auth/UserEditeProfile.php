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
            'mobile' => [
                'required', 'string', 'regex:/^05\d{8}$/', 'max:15',
                Rule::unique('users', 'mobile')->ignore(auth()->id()),
            ],
            'name' => ['required', 'string', 'max:25', 'min:2', ],
        ];
    }

    public function attributes()
    {
        return [
            'mobile' => __('mobile'),
            'name' => __('name'),
        ];
    }

}
