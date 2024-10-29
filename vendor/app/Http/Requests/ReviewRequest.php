<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', Rule::in(['product', 'renter'])],
            'rating' => ['required', 'numeric', 'min:1', 'max:5'],
            'comment' => ['sometimes', 'nullable']
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required' => 'Please select your rating',
            'rating.min' => 'rating should be from 1 to 5 stars',
            'rating.max' => 'rating should be from 1 to 5 stars',
            'type.required' => 'indicate the type of review (product or renter).',
            'type.in' => 'review type must be either renter or product',
        ];
    }
}
