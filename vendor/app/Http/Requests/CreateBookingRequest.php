<?php

namespace App\Http\Requests;

use App\Http\CustomValidationRule\ValidateProductQuantity;
use App\Http\CustomValidationRule\ValidateProductUnavailability;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateBookingRequest extends FormRequest
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
            'quantity' => ['required', 'integer', new ValidateProductQuantity()],
            'startDate' => ['required', 'date', 'date_format:Y-m-d'],
            'endDate' => ['required', 'date', 'date_format:Y-m-d'],
            'exchangeSchedule.city' => ['required', 'string'],
            'exchangeSchedule.originName' => ['required', 'string'],
            'exchangeSchedule.country' => ['nullable', 'string'],
            'exchangeSchedule.countryCode' => ['nullable', 'string'],
            'exchangeSchedule.longitude' => ['required', 'numeric'],
            'exchangeSchedule.latitude' => ['required', 'numeric'],
            'exchangeSchedule.timeOfExchange' => ['required', 'date_format:h:ia'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'exchangeSchedule.city.required' => 'Select city for exchange location',
            'exchangeSchedule.originName.required' => 'Select origin name for exchange location',
            'exchangeSchedule.longitude.required' => 'Select longitude for exchange location',
            'exchangeSchedule.latitude.required' => 'Select latitude for exchange location',
            'exchangeSchedule.timeOfExchange.required' => 'Select time for exchange',
            'exchangeSchedule.timeOfExchange.date_format' => 'Time of exchange must match the format h:ia. eg: 12:00am or 05:50pm',
        ];
    }
}
