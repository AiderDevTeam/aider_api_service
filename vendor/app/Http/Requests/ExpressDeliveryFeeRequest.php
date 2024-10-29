<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ExpressDeliveryFeeRequest extends FormRequest
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
            'cartExternalIds' => ['required', 'array'],
            'destinationCountry' => ['required', 'string'],
            'destinationName' => ['required', 'string'],
            'destinationState' => ['required', 'string'],
            'destinationLatitude' => ['required', 'numeric'],
            'destinationLongitude' => ['required', 'numeric']
        ];
    }
}
