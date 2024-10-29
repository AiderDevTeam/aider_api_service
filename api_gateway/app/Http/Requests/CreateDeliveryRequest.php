<?php

namespace App\Http\Requests;

use App\Models\Delivery;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateDeliveryRequest extends FormRequest
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
            'deliveryExternalId' => ['required', 'string', Rule::unique('deliveries', 'external_id')],
            'currency' => ['required', 'string'],
            'deliveryOption' => ['required', 'string', Rule::in(Delivery::DELIVERY_OPTIONS)],
            'isPickup' => ['required', 'boolean', Rule::in([true, false])],
            'isFulfillmentDelivery' => ['required', 'boolean', Rule::in([true, false])],
            'callbackUrl' => ['required', 'string'],
            'amountToCollect' => ['nullable'],
            'serviceType' => ['required', 'string', Rule::in(Delivery::DELIVERY_SERVICE_TYPES)],
            'isPrepaidDelivery' => ['required', 'boolean', Rule::in([true, false])],
            'pickUpAt' => ['required', 'string'],
            'recipient.name' => ['required', 'string'],
            'recipient.phone' => ['required', 'numeric'],
            'sender.name' => ['required', 'string'],
            'sender.phone' => ['required', 'numeric'],
            'destination.name' => ['required', 'string'],
            'destination.city' => ['required', 'string'],
            'destination.state' => ['required', 'string'],
            'destination.country' => ['required', 'string'],
            'destination.countryCode' => ['required', 'string'],
            'destination.latitude' => ['required', 'numeric'],
            'destination.longitude' => ['required', 'numeric'],
            'origin.name' => ['required', 'string'],
            'origin.city' => ['required', 'string'],
            'origin.state' => ['required', 'string'],
            'origin.country' => ['required', 'string'],
            'origin.countryCode' => ['required', 'string'],
            'origin.latitude' => ['required', 'numeric'],
            'origin.longitude' => ['required', 'numeric'],
            'items' => ['required', 'array'],
            'items.*.name' => ['required', 'string'],
            'items.*.type' => ['required', 'string'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:1'],
            'items.*.weight' => ['required', 'numeric', 'min:1'],
        ];
    }
}
