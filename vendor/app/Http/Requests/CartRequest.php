<?php

namespace App\Http\Requests;

use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CartRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'vendorId' => ['required', Rule::in(Vendor::query()->pluck('id'))],
            'productId' =>['required', Rule::in(Product::query()->pluck('id'))],
            'quantity' => ['required', 'integer', 'min:1'],
            'size' => ['sometimes', 'nullable'],
            'color' => ['sometimes', 'nullable'],
            'incremental' => ['required', 'boolean'],
            'recipient.name' => ['nullable', 'string'],
            'recipient.phone' => ['nullable', 'numeric'],
            'userType' => ['nullable', 'sometimes']
        ];
    }
}
