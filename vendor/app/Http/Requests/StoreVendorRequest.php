<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVendorRequest extends FormRequest
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
            'businessName' => ['nullable', 'string', 'min:2', 'max:50'],
            'shopTag' => ['required', 'string', 'min:2', 'max:50', Rule::unique('vendors', 'shop_tag')],
            'accountNumber' => ['nullable', 'string', 'size:10'],
            'sortCode' => ['nullable', 'string'],
            'accountName' => ['nullable', 'string'],
            'originName' => ['nullable', 'string'],
            'city' => ['required', 'string'],
            'state' => ['sometimes', 'nullable'],
            'locationResponse' => ['sometimes', 'nullable'],
            'longitude' => ['required'],
            'latitude' => ['required'],
            'categoriesIds' => ['nullable', 'array', Rule::in(Category::query()->pluck('id'))],
            'shopLogo' => ['sometimes', 'nullable'],
            'shopLogoFileType' => ['sometimes', Rule::in(array_values(SHOP_LOGO_FILE_TYPES))],
            'availabilities' => ['sometimes', 'array'],
            'availabilities.*.day' => ['sometimes', 'string'],
            'availabilities.*.openingTime' => ['sometimes', 'string'],
            'availabilities.*.closingTime' => ['sometimes', 'string'],
            'default' => ['sometimes', 'boolean', Rule::in([true, false])],
        ];
    }
}
