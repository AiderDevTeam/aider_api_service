<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentVerificationRequest extends FormRequest
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
            'docImage' => ['required', 'string'],
            'docCountry' => ['required', 'string'],
            'docType' => ['required', 'string', Rule::in(['PP', 'DL'])],
            'selfieImage' => ['required', 'string']
        ];
    }
}
