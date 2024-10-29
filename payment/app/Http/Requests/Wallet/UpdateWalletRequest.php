<?php

namespace App\Http\Requests\Wallet;

use App\Models\Wallet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UpdateWalletRequest extends FormRequest
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
//            'accountNumber' => [
//                'required_if:type,' . Wallet::MOMO . ',' . Wallet::BANK,
//                'string', Rule::unique('wallets', 'account_number')
//            ],
//            'accountName' => ['sometimes', Rule::unique('wallets', 'account_name')],
//            'type' => ['required', 'string', Rule::in([Wallet::MOMO, Wallet::BANK, Wallet::POYNT])],
//            'sortCode' => ['sometimes', 'string'],
        ];
    }

    protected function failedValidation(Validator|\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
        ]));
    }
}
