<?php

namespace App\Http\Requests;

use App\Models\Campaign;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetReferralLinkRequest extends FormRequest
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
            'userExternalId' => ['required', 'string'],
            'campaignId' => ['required', Rule::in(Campaign::pluck('id')), 'has_campaign_expired']
        ];
    }

    public  function messages(){
        return [
            "campaignId.required" => "Oops! please try again later",
            "campaignId.has_campaign_expired" => 'The campaign must have expired, please contact poynt support for assistance'
        ];
    }
}
