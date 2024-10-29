<?php

namespace App\Http\Requests\VendorService;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
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
            'productExternalId' => ['required', 'string'],
            'subCategoryId' => ['sometimes', 'integer'],
            'name' => ['sometimes', 'string'],
            'description' => ['sometimes', 'string'],
            'condition' => ['sometimes', 'string'],
            'size' => ['sometimes', 'string'],
            'color' => ['sometimes', 'string'],
            'quantity' => ['sometimes', 'numeric'],
            'weight' => ['sometimes', 'numeric'],
            'unitPrice' => ['sometimes', 'numeric'],
            'status' => ['sometimes', 'string'],
            'rejectionReason' => ['sometimes', 'string'],
            'tags' => ['sometimes', 'array']
        ];
    }
}
