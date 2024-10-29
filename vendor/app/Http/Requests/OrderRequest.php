<?php

namespace App\Http\Requests;

use App\Models\Cart;
use App\Models\Delivery;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'cartExternalIds' => ['array', 'required', Rule::in(Cart::query()->pluck('external_id'))],
            'destination' => ['required'],
            'deliveryAmount' => ['sometimes'],
            'walletExternalId', ['sometimes', 'nullable'],
            'recipientContact' => ['sometimes', 'nullable'],
            'recipientAlternativeContact' => ['sometimes', 'nullable'],
            'recipientSortCode' => ['sometimes', 'nullable'],
            'deliveryOption' => ['required', 'string', Rule::in(Delivery::DELIVERY_OPTIONS)],
            'recipient.name' => ['required', 'string'],
            'recipient.phone' => ['required', 'numeric'],
            'destination.name' => ['required', 'string'],
            'destination.city' => ['sometimes', 'nullable'],
            'destination.state' => ['required', 'string'],
            'destination.country' => ['required', 'string'],
            'destination.countryCode' => ['sometimes', 'nullable'],
            'destination.latitude' => ['required', 'numeric'],
            'destination.longitude' => ['required', 'numeric'],
            'userType' => ['nullable', 'sometimes'],
            'payOnDelivery' => ['sometimes', 'boolean']
        ];
    }

    public function messages(): array
    {
        return [
            'recipient.name.required' => 'Please enter name of recipient',
            'recipient.phone.required' => 'Please enter phone number of recipient',
            'destination.name.required' => 'Please enter destination name',
            'destination.city.required' => 'Please enter destination city',
            'destination.state.required' => 'Please enter destination state',
            'destination.country.required' => 'Please enter destination country',
            'destination.latitude.required' => 'Please enter destination location',
            'destination.longitude.required' => 'Please enter destination location'
        ];
    }

}
