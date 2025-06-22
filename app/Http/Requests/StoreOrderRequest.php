<?php

namespace App\Http\Requests;

use App\Enum\PaymentMethodEnum;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'service_id' => 'required|exists:services,id,deleted_at,NULL,is_active,1',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'location_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'internal_note'=>'nullable|string|max:1000',
            'images' => 'nullable|array',
            'images.*' => 'nullable|mimes:jpg,jpeg,png,gif,webp,avi,mkv|max:10240',
            'sounds' => 'nullable|array',
            'sounds.*' => 'nullable|mimes:mp3,wav,ogg,m4a|max:10240',

            'paymentmethod' =>'required','integer|in:'.PaymentMethodEnum::moyasar->value.'',

        ];
    }

}
