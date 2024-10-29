<?php

namespace App\Http\Requests;

use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionRequest extends FormRequest
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
            'userExternalId' => [Rule::requiredIf($this->type === Payment::DISBURSEMENT), 'string'],
            'paymentTypeExternalId' => ['required', 'string'],
            'amount' => ['required', 'numeric'],
            'accountNumber' => ['sometimes', 'string', 'nullable'],
            'sortCode' => ['sometimes', 'string', 'nullable'],
            'type' => ['required', 'string', Rule::in(array_values(Transaction::TYPES))],
            'paymentType' => ['required', 'string', Rule::in(['booking'])]
        ];
    }
}
