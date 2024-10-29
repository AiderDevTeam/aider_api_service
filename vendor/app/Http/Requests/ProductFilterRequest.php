<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProductFilterRequest extends FormRequest
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
            'subCategoryItemIds' => ['sometimes', 'array', 'min:1'],
            'priceRange.min' => ['sometimes', 'numeric'],
            'priceRange.max' => ['sometimes', 'numeric'],
            'location' => ['sometimes', 'array', 'min:1']
        ];
    }

    public function messages(): array
    {
        return [
            'subCategoryItemIds.min' => 'select at least one category for your filter',
            'location.min' => 'select at lease one location for your filter'
        ];
    }
}
