<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IdNumberAndFaceVerificationRequest extends FormRequest
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
            'number' => ['required', 'string'],
            'image' => ['required', 'string'],
            'type' => ['required', 'string', Rule::in(['NIN', 'BVN'])]
        ];
    }

    public function messages(): array
    {
        return [
            'number.required' => 'id number is required',
            'type.in' => 'type should be NIN or BVN'
        ];
    }
}
