<?php

namespace App\Http\Requests;

use App\Custom\BookingStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookingPickupConfirmationRequest extends FormRequest
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
            'status' => ['required', 'string', Rule::in([BookingStatus::SUCCESS, BookingStatus::FAILED])],
            'type' => ['required', 'string', Rule::in(['vendor', 'user'])]
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => ':attribute must be either '. BookingStatus::SUCCESS . ' or '. BookingStatus::FAILED,
            'type.in' => ':attribute must be either vendor or user'
        ];
    }
}
