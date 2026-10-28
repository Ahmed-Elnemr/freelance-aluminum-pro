<?php

namespace App\Http\Requests\api;

use App\Enum\OrderRatingTag;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRatingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'stars' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:500'],
            'tags' => ['nullable', 'array', 'max:10'],
            'tags.*' => ['string', Rule::in(OrderRatingTag::values())],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'stars.required' => __('ratings.stars_between'),
            'stars.integer' => __('ratings.stars_between'),
            'stars.between' => __('ratings.stars_between'),
            'comment.max' => __('ratings.comment_max'),
            'tags.array' => __('ratings.tags_invalid'),
            'tags.max' => __('ratings.tags_invalid'),
            'tags.*.string' => __('ratings.tags_invalid'),
            'tags.*.in' => __('ratings.tags_invalid'),
        ];
    }
}
