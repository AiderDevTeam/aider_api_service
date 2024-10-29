<?php

namespace App\Http\Requests;

use App\Models\Transaction;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DisbursementRequest extends FormRequest
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
            'transactionId' => ['required', 'string'],
            'amount' => ['required', 'integer', 'min:10'],
            'rSwitch' => ['required'],
            'accountNumber' => ['required', 'digits:10'],
            'recipientCode' => ['sometimes', 'string'],
            'type' => ['required', Rule::in(Transaction::DISBURSEMENT_TYPES)],
            'description' => ['sometimes', 'string'],
            'callbackUrl' => ['required', 'url']
        ];
    }

    public function messages(): array
    {
        return [
            'amount.digits' => 'amount must be in kobo eg. 1005 kobo for ₦ 10.50',
            'amount.min' => 'Minimum amount is 10 kobo (₦ 0.10)'
        ];
    }
}
