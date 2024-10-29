<?php

namespace App\Http\Requests;

use App\Models\Admin;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAdminRequest extends FormRequest
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
            'firstName' => ['required', 'string', 'min:2'],
            'lastName' => ['required', 'string', 'min:2'],
            'email' => ['required', 'email', Rule::unique('admins', 'email')],
            'gender' => ['required', Rule::in(Admin::GENDERS)],
            'phone' => ['required', 'digits:10', Rule::unique('admins', 'phone')],
            'birthday' => ['sometimes', 'nullable'],
            'password' => ['required', 'string', 'min:6'],
        ];
    }
}
