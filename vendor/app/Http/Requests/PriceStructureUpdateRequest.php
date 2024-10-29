<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PriceStructureUpdateRequest extends FormRequest
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
            'name' => ['sometimes', 'string', Rule::unique('price_structures', 'name')->ignore($this->name, 'name')],
            'description' => ['sometimes', 'string'],
            'startDay' => ['sometimes', 'integer'],
            'endDay' => ['sometimes', 'integer'],
        ];
    }
}
