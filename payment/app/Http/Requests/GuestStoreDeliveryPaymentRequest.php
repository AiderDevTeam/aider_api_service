<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GuestStoreDeliveryPaymentRequest extends FormRequest
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
            'vendorExternalId' => ['required', 'string'],
            'guestExternalId' => ['sometimes', 'nullable', 'string'],
            'deliveryExternalId' => ['required', 'string', Rule::unique('delivery_payments', 'delivery_external_id')],
            'description' => ['sometimes', 'string'],
            'amount' => ['required'],
            'callbackUrl' => ['required', 'url'],
            'collectionWallet.externalId' => ['nullable', 'string'],
            'collectionWallet.accountNumber' => ['required', 'string'],
            'collectionWallet.sortCode' => ['required', 'string'],
            'collectionWallet.accountName' => ['required', 'string'],
        ];
    }
}
