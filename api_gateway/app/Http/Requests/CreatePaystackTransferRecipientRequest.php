<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePaystackTransferRecipientRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', Rule::in(['nuban', 'mobile_money', 'basa', 'ghipss'])],
            'name' => ['required', 'string'],
            'accountNumber' => ['required', 'string'],
            'bankCode' => ['required', 'string'],
            'currency' => ['required', 'string', Rule::in(['NGN'])]
        ];
    }
}
