<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateDeliveryFeeRequest extends FormRequest
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
            'processor' => ['required'],
            'deliveryOption' => ['required'],
            'fee' => ['required', 'numeric'],
            'margin' => ['required', 'numeric'],
            'discountedFee' => ['sometimes'],
            'payOnDeliveryFee' => ['sometimes', 'numeric'],
            'payOnDeliveryFeeMargin' => ['sometimes', 'numeric'],
        ];
    }
}
