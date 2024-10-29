<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateVendorRequest extends FormRequest
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
            'shopLogo' => ['sometimes', 'string', Rule::requiredIf(fn() => $this->has('shopLogo'))],
            'businessName' => ['sometimes', 'string', 'min:2', 'max:50'],
            'shopTag' => ['sometimes', 'string', 'min:2', 'max:50', Rule::unique('vendors', 'shop_tag')->ignore(request()->vendor->id)],
            'walletNumber' => ['sometimes', 'string', 'digits:10'],
            'city' => ['sometimes'],
            'state' => ['sometimes'],
            'longitude' => ['sometimes'],
            'latitude' => ['sometimes'],
            'locationResponse' => ['sometimes'],
            'type' => ['sometimes'],
            'official' => ['sometimes'],
            'payOnDelivery' => ['sometimes']
        ];
    }
}
