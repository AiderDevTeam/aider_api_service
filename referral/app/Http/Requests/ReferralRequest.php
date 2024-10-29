<?php

namespace App\Http\Requests;

use App\Models\Campaign;
use App\Models\CampaignChannel;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class ReferralRequest extends FormRequest
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
            'campaignId' => ['sometimes', 'has_campaign_expired'],
            'referredId' => ['required'],
            //'campaignChannelId' => ['required', Rule::in(CampaignChannel::pluck('id'))],
            'code' => ['sometimes'],
            'referralLink' => ['required', 'referral_link_exists'],
        ];
    }

    public function messages()
    {
        $channels = implode(',', CampaignChannel::pluck('id')->toArray());
        return [
            //'campaignId.required' => 'kindly pass the id of the campaign where the referral will be under',
            "campaignId.has_campaign_expired" => 'The campaign must have expired, please contact poynt support for assistance',
            'referredId.required' => "Enter a user's external Id(userExternalId)",
            //'campaignChannelId.required' => "Enter a campaign channel($channels)",
            'referralLink.required' => "Add the referral link from firebase",
            //'referralLink.owns_referral_link' => "Please check referral link, you cannot refer yourself to this campaign",
            'referralLink.referral_link_exists' => "Referral link does not exist!",
            'code.sometimes' => "Add the referral code  from the firebase referral link"
        ];
    }
}
