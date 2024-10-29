<?php

namespace App\Http\Requests;

use App\Custom\Status;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KYCApprovalRequest extends FormRequest
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
            'userExternalId' => ['required', 'string'],
            'verificationStatus' => ['required', 'string', Rule::in(Status::ID_VERIFICATION_STATUSES)],
            'rejectionReason' => ['string', Rule::requiredIf($this->verificationStatus === Status::REJECTED), Rule::in(Status::KYC_REJECTION_REASONS)]
        ];
    }
}
