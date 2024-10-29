<?php

namespace App\Http\Requests;

use App\Models\Section;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSectionRequest extends FormRequest
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
            'name' => ['sometimes', 'string', Rule::unique('sections', 'name')->ignore($this->name, 'name')],
            'live' => ['sometimes', 'boolean', Rule::in([true, false])],
            'position' => ['sometimes', 'integer'],
            'type' => ['sometimes', 'string', Rule::in(Section::SECTION_TYPES)],
            'filter' => ['sometimes', 'array'],
            'filters' => ['sometimes', 'array'],
            'additionalData' => ['sometimes', 'array', 'nullable'],
            'page' => ['sometimes', 'string', Rule::in(array_values(Section::SECTION_PAGE_TYPES))],
        ];
    }
}
