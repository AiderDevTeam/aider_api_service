<?php

namespace App\Http\Requests;

use App\Models\ActionPoynt;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreActionPoyntRequest extends FormRequest
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
            'action' => ['required', 'string'],
            'poynt' => ['required', 'numeric'],
            'type' => ['required', 'string', Rule::in(ActionPoynt::ACTION_TYPES)]
        ];
    }
}
