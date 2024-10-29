<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SmsNotificationRequest extends FormRequest
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
            'userExternalId' => ['sometimes'],
            'phone' => ['string', Rule::requiredIf(!$this->has('userExternalId')), Rule::excludeIf($this->has('userExternalId'))],
            'message' => ['string']
        ];
    }
}
