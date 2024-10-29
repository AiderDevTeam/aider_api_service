<?php

namespace App\Http\Requests;

use App\Models\Section;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSectionRequest extends FormRequest
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
            'externalId' => ['required', 'string', Rule::unique('sections', 'external_id')],
            'name' => ['required', 'string', Rule::unique('sections', 'name')],
            'live' => ['required', 'boolean', Rule::in([true, false])],
            'position' => ['required', 'integer'],
            'type' => ['required', 'string', Rule::in(Section::SECTION_TYPES)],
            'additionalData' => ['sometimes', 'array', 'nullable'],
            'filter' => ['sometimes', 'array'],
            'filters' => ['sometimes', 'array'],
            'page' => ['sometimes', 'string', Rule::in(array_values(Section::SECTION_PAGE_TYPES))],
        ];
    }
}
