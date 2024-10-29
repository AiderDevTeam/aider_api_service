<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
            'firstName' => ['sometimes', 'string', 'min:2', 'max:50'],
            'lastName' => ['sometimes', 'string', 'min:2', 'max:50'],
            'displayName' => ['sometimes', 'string', 'min:2', Rule::unique('users', 'display_name')->ignore($this->displayName, 'display_name')],
            'email' => ['sometimes', 'string', Rule::unique('users', 'email')->ignore($this->email, 'email')],
            'birthday' => ['sometimes', 'date_format:Y-m-d'],
            'gender' => ['sometimes', Rule::in(User::GENDERS)],
            'phone' => ['sometimes', Rule::unique('users', 'phone')->ignore($this->phone, 'phone')],
            'callingCode' => ['sometimes', 'string'],
            'status' => ['sometimes', 'string'],
            'pushNotificationToken' => ['sometimes', 'string'],
            'referralCode' => ['sometimes', 'nullable', 'string'],
            'referralUrl' => ['sometimes', 'nullable', 'string'],
            'deviceOs' => ['sometimes', Rule::in(['android', 'ios'])],
            'password' => ['sometimes', 'string', 'min:6'],
            'profilePhoto' => ['sometimes', 'nullable'],
            'address.latitude' => ['sometimes', 'numeric'],
            'address.longitude' => ['sometimes', 'numeric'],
            'address.originName' => ['sometimes', 'string'],
            'address.city' => ['sometimes', 'string'],
            'address.country' => ['sometimes', 'string'],
            'address.countryCode' => ['sometimes', 'string'],
            'canReceiveEmailUpdates' => ['sometimes', 'boolean'],
            'canReceivePushNotifications' => ['sometimes', 'boolean'],
            'canReceiveSms' => ['sometimes', 'boolean'],
        ];
    }
}
