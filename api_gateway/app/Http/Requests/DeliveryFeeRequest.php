<?php

namespace App\Http\Requests;

use App\Http\Services\Delivery\ShaqExpressService;
use App\Models\ShaqExpressDelivery;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeliveryFeeRequest extends FormRequest
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
            'destinationCountry' => ['sometimes', 'string', 'nullable'],
            'destinationName' => ['sometimes', 'string', 'nullable'],
            'destinationState' => ['sometimes', 'string', 'nullable'],
            'originCountry' => ['sometimes', 'string', 'nullable'],
            'originName' => ['sometimes', 'string', 'nullable'],
            'originState' => ['sometimes', 'string', 'nullable'],
            'routes.origin.latitude' => ['sometimes', 'nullable', 'numeric'],
            'routes.origin.longitude' => ['sometimes', 'nullable', 'numeric'],
            'routes.destination.latitude' => ['sometimes', 'nullable', 'numeric'],
            'routes.destination.longitude' => ['sometimes', 'nullable', 'numeric'],
            'items.*.name' => ['sometimes', 'nullable', 'string'],
            'items.*.type' => ['sometimes', 'nullable', 'string'],
            'items.*.addInsurance' => ['sometimes', 'nullable', 'boolean'],
            'items.*.quantity' => ['sometimes', 'nullable', 'numeric'],
            'items.*.price' => ['sometimes', 'nullable', 'numeric'],
            'items.*.weight' => ['sometimes', 'nullable', 'numeric'],
            'items.*.isFragile' => ['sometimes', 'nullable', 'boolean'],
            'vehicleType' => ['sometimes', 'nullable', 'string', Rule::in(ShaqExpressDelivery::VEHICLE_TYPES)]
        ];
    }
}
