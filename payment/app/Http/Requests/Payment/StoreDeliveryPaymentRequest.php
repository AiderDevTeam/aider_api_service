<?php

namespace App\Http\Requests\Payment;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class StoreDeliveryPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vendorExternalId' => ['required', 'string'],
            'guestExternalId' => ['sometimes', 'nullable', 'string'],
            'deliveryExternalId' => ['required', 'string', Rule::unique('delivery_payments', 'delivery_external_id')],
            'description' => ['sometimes', 'string'],
            'amount' => ['required'],
            'callbackUrl' => ['required', 'url'],
            'collectionWallet.externalId' => ['required', 'string'],
            'collectionWallet.accountNumber' => ['required', 'string'],
            'collectionWallet.sortCode' => ['required', 'string'],
            'collectionWallet.accountName' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'deliveryExternalId.unique' => 'There is already a payment for this delivery.',
        ];
    }

    protected function failedValidation(Validator|\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
        ]));
    }

}

