<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AuthenticationRequest extends FormRequest
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
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
            'deviceOs' => ['required', Rule::in(['android', 'ios'])],
            'pushNotificationToken' => ['sometimes', 'nullable', 'string']
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Enter your email',
        ];
    }
}
