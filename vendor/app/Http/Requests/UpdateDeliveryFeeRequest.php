<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDeliveryFeeRequest extends FormRequest
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
            'deliveryOption' => ['sometimes', 'nullable'],
            'fee' => ['sometimes', 'nullable'],
            'margin' => ['sometimes', 'nullable'],
            'discountedFee' => ['sometimes', 'nullable'],
            'payOnDeliveryFee' => ['sometimes', 'nullable'],
            'payOnDeliveryFeeMargin' => ['sometimes', 'nullable'],
        ];
    }
}
