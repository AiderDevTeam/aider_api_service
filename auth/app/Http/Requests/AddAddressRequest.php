<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddAddressRequest extends FormRequest
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
            'destinationName' => ['required', 'string'],
            'state' => ['required', 'string'],
            'city' => ['sometimes', 'nullable'],
            'country' => ['required', 'string'],
            'countryCode' => ['sometimes', 'nullable'],
            'longitude' => ['required', 'numeric'],
            'latitude' => ['required', 'numeric'],
            'firstName' => ['required', 'string'],
            'lastName' => ['required', 'string'],
            'phone' => ['required', 'numeric'],
            'alternativePhoneNumber' => ['sometimes', 'nullable'],
            'additionalInformation' => ['sometimes', 'nullable'],
            'default' => ['required', 'boolean', Rule::in([true, false])]
        ];
    }
}
