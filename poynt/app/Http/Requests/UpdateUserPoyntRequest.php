<?php

namespace App\Http\Requests;

use App\Models\ActionPoynt;
use App\Models\UserActionPoynt;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserPoyntRequest extends FormRequest
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
            'type' => ['required', 'string', Rule::in(UserActionPoynt::ACTION_POYNT_TYPES)],
            'action' => [
                'string',
                Rule::in(ActionPoynt::query()->pluck('action')),
                Rule::requiredIf(fn()=>($this->type === UserActionPoynt::ACTION_POYNT_TYPES['CREDIT']))
            ],
            'debitPoynt' => [
                'numeric',
                Rule::requiredIf(fn()=>($this->type === UserActionPoynt::ACTION_POYNT_TYPES['DEBIT']))
            ],
            'actionResponsePayload' => ['required', 'array'],
            'actionValue' => ['sometimes', 'numeric'],
        ];
    }
}
