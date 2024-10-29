<?php

namespace App\Http\Requests\Payment;

use App\Models\VASPayment;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class StoreVASPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Change this to your desired authorization logic.
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', Rule::in([VASPayment::AIRTIME_TOP_UP, VASPayment::DATA_BUNDLE_PURCHASE])],
            'description' => ['sometimes', 'string'],
            'amount' => ['required'],
            'bundleValue' => [Rule::requiredIf(fn() => ($this->type === VASPayment::DATA_BUNDLE_PURCHASE))],
            'wallets.collection' => ['required'],
            'wallets.disbursement' => ['required'],

        ];
    }

    protected function failedValidation(Validator|\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
        ]));
    }

    public function messages(): array
    {
        return [
            'wallets.collection.required' => 'The collection wallet field is required.',
            'wallets.disbursement.required' => 'The disbursement wallet field is required.'
        ];
    }
}

