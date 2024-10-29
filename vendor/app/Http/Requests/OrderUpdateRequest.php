<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderUpdateRequest extends FormRequest
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
            'status' => ['sometimes', 'string'],
            'collectionStatus' => ['sometimes', 'string'],
            'deliveryAmount' => ['sometimes', 'numeric'],
            'discountedAmount' => ['sometimes', 'numeric'],
            'itemsAmount' => ['sometimes', 'numeric'],
            'destination' => ['sometimes', 'string'],
            'description' => ['sometimes', 'string'],
            'recipientContact' => ['sometimes', 'string'],
            'recipientSortCode' => ['sometimes', 'string'],
            'recipientAlternativeContact' => ['sometimes', 'string'],
            'isAccepted' => ['sometimes', 'boolean', Rule::in(true, false)],
            'disbursementStatus' => ['sometimes', 'string'],
            'disbursementAmount' => ['sometimes', 'numeric'],
            'payoutCommission' => ['sometimes', 'numeric']
        ];
    }
}
