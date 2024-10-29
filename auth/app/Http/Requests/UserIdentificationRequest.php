<?php

namespace App\Http\Requests;

use App\Custom\Identification;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserIdentificationRequest extends FormRequest
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
            'idNumber' => ['sometimes', 'nullable'],
            'documentImage' => [
                Rule::requiredIf(in_array($this->type, array_values(Identification::DOCUMENT_TYPES)))
            ],
            'selfie' => ['required'],
            'type' => ['required', Rule::in(array_values(Identification::TYPES))],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'select an identification type',
            'type.in' => 'type should be one of these: ' . implode(',', array_values(Identification::TYPES)),
            'documentImage.required' => 'provide an image of your document',
            'selfie.required' => 'provide a selfie of yourself'
        ];
    }
}
