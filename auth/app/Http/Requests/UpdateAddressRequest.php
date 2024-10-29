<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAddressRequest extends FormRequest
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
            'destinationName' => ['sometimes', 'string'],
            'state' => ['sometimes', 'string'],
            'city' => ['sometimes', 'string'],
            'country' => ['sometimes', 'string'],
            'countryCode' => ['sometimes', 'string'],
            'longitude' => ['sometimes', 'numeric'],
            'latitude' => ['sometimes', 'numeric'],
            'firstName' => ['sometimes', 'string'],
            'lastName' => ['sometimes', 'string'],
            'phone' => ['sometimes', 'numeric'],
            'alternativePhoneNumber' => ['sometimes', 'nullable'],
            'additionalInformation' => ['sometimes', 'nullable'],
            'default' => ['sometimes', 'boolean', Rule::in([true, false])]
        ];
    }
}
