<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserSignupRequest extends FormRequest
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
            'firstName' => ['required', 'string', 'min:2', 'max:50'],
            'lastName' => ['required', 'string', 'min:2', 'max:50'],
            'displayName' => ['required', 'string', 'min:2', Rule::unique('users', 'display_name')],
            'email' => ['required', 'string', Rule::unique('users', 'email')],
            'birthday' => ['required', 'string', 'date_format:Y-m-d'],
            'gender' => ['required', Rule::in(User::GENDERS)],
            'phone' => ['required', Rule::unique('users', 'phone')],
            'userTypeId' => ['sometimes', 'integer', Rule::exists('user_types', 'id')],
            'callingCode' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6'],
            'pushNotificationToken' => ['sometimes', 'nullable', 'string'],
            'referralCode' => ['sometimes', 'nullable', 'nullable', 'string'],
            'referralUrl' => ['sometimes', 'nullable', 'string'],
            'deviceOs' => ['sometimes', 'nullable', Rule::in(['android', 'ios'])],
            'termsAndConditions' => ['required', 'boolean'],
            'referralLink' => ['sometimes', 'nullable'],
            'address.latitude' => ['required', 'numeric'],
            'address.longitude' => ['required', 'numeric'],
            'address.originName' => ['sometimes', 'string'],
            'address.city' => ['required', 'string'],
            'address.country' => ['required', 'string'],
            'address.countryCode' => ['sometimes', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
//            'userTypeId.required' => 'Select a user type',
            'userTypeId.exists' => 'Select a valid user type'
        ];
    }
}
