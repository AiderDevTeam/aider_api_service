<?php

namespace App\Http\Requests\Wallet;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWalletRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'accountNumber' => ['required', 'string'],
            'accountName' => ['sometimes', 'string'],
            'sortCode' => ['sometimes', 'string'],
            'bankCode' => ['required', 'numeric', Rule::exists('banks', 'bank_code')],
        ];
    }
}
