<?php

namespace App\Http\Requests;

use App\Custom\Status;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
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
            'name' => ['sometimes', 'string', 'max:50', 'min:2', Rule::unique('categories', 'name')->ignore($this->name, 'name')],
            'status' => ['sometimes', 'string', Rule::in([Status::ACTIVE, Status::INACTIVE])],
            'imageUrl' => ['sometimes', 'string']
        ];
    }
}
