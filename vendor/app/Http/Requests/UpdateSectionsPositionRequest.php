<?php

namespace App\Http\Requests;

use App\Models\Section;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSectionsPositionRequest extends FormRequest
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
            'sections' => ['required', 'array'],
            'sections.*.externalId' => ['required', 'string', Rule::exists('sections', 'external_id')],
            'sections.*.position' => ['required', 'integer'],
        ];
    }
}
