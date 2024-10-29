<?php

namespace App\Http\Requests;

use App\Custom\Status;
use App\Http\CustomValidationRule\ValidateProductAddress;
use App\Http\CustomValidationRule\ValidateProductPrices;
use App\Models\Product;
use App\Models\ProductRejection;
use App\Models\SubCategory;
use App\Models\WeightUnit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductDetailsUpdateRequest extends FormRequest
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
            'subCategoryItemId' => ['sometimes', 'integer', Rule::exists('sub_category_items', 'id')],
            'name' => ['sometimes', 'string'],
            'status' => ['sometimes', 'string', Rule::in(Product::STATUSES)],
            'description' => ['sometimes', 'string'],
            'quantity' => ['sometimes', 'numeric'],
            'value' => ['sometimes', 'numeric'],
            'prices' => ['sometimes', 'array'],
            'prices.*.price' => ['sometimes', 'numeric'],
            'prices.*.productPriceId' => ['sometimes', 'nullable'],
            'prices.*.priceStructureId' => ['sometimes', 'nullable'],
            'address' => ['sometimes', 'array', 'nullable'],
            'address.city' => ['sometimes', 'string'],
            'address.originName' => ['sometimes', 'string'],
            'address.country' => ['sometimes', 'string'],
            'address.countryCode' => ['sometimes', 'string'],
            'address.longitude' => ['sometimes', 'numeric'],
            'address.latitude' => ['sometimes', 'numeric'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'product status should be one of the following: ' . implode(',', Product::STATUSES)
        ];
    }
}
