<?php

namespace App\Http\Requests\auth;

use Illuminate\Foundation\Http\FormRequest;

class CheckMobileOtpRequest extends FormRequest
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
            'mobile' => 'required|exists:users,mobile,deleted_at,NULL',
            'code' => 'required|digits:4',
        ];
    }

//    public function messages(): array
//    {
//        return [
//            'mobile.required' => __('validation.mobile_required'),
//            'mobile.exists' => __('validation.mobile_exists'),
//            'code.required' => __('validation.code_required'),
//            'code.digits' => __('validation.code_digits'),
//        ];
//    }
}
