<?php

namespace App\Http\Requests;

use App\Http\CustomValidationRule\EnsureAllPriceStructuresAreSelectedValidation;
use App\Http\CustomValidationRule\ValidateProductAddress;
use App\Http\CustomValidationRule\ValidateProductPrices;
use App\Http\CustomValidationRule\ValidateProductPricesExistence;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\Vendor;
use App\Models\WeightUnit;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
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
            'subCategoryItemId' => ['required', 'integer', Rule::exists('sub_category_items', 'id')],
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'quantity' => ['required', 'numeric'],
            'value' => ['required', 'numeric'],
            'prices' => [
                'required', 'array',
                new ValidateProductPrices(),
                new ValidateProductPricesExistence(),
                new EnsureAllPriceStructuresAreSelectedValidation()
            ],
            'photos*' => ['required', 'image'],
            'address' => ['sometimes', 'string', new ValidateProductAddress()],
        ];
    }

    public function messages(): array
    {
        return [
            'subCategoryItemId.exists' => 'selected sub category not found',
        ];
    }
}
