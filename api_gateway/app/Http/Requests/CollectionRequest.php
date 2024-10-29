<?php

namespace App\Http\Requests;

use App\Models\Transaction;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CollectionRequest extends FormRequest
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
            'transactionId' => ['required', 'string', Rule::unique('transactions', 'external_id')],
            'amount' => ['required', 'integer', 'min:10'],
            'rSwitch' => ['required', 'in:MTN,VOD,ATL,TGO,AIR'],
            'accountNumber' => ['required', 'digits:10'],
            'description' => ['sometimes', 'string'],
            'callbackUrl' => ['required', 'url']
        ];
    }

    public function messages(): array
    {
        return [
            'amount.digits' => 'amount must be in pesewas eg. 1005 pesewas for GHC 10.50',
            'amount.min' => 'Minimum amount is 10 pesewas (GHC 0.10)'
        ];
    }
}
