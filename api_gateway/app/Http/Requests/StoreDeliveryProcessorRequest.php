<?php

namespace App\Http\Requests;

use App\Models\DeliveryProcessor;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDeliveryProcessorRequest extends FormRequest
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
            'name' => [
                'required', 'string',
                Rule::in(array_values(DeliveryProcessor::PROCESSORS)),
                Rule::unique('delivery_processors', 'name')
            ],
            'active' => ['required', 'boolean', Rule::in(true, false)],
            'express' => ['required', 'boolean', Rule::in(true, false)],
            'nextDay' => ['required', 'boolean', Rule::in(true, false)],
        ];
    }

    public function messages(): array
    {
        return [
          'name.unique' => 'Processor already exist'
        ];
    }
}
