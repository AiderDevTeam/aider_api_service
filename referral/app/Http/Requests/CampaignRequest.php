<?php

namespace App\Http\Requests;

use App\Models\CampaignType;
use App\Models\RewardSplit;
use App\Models\RewardType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CampaignRequest extends FormRequest
{

    private  $campainType;
    private  $rewardSplit;
    private  $rewardType;
    private $cash;
    private $point;


    public function __construct()
    {
        $this->campainType = CampaignType::pluck('id')->toArray();
        $this->rewardSplit = RewardSplit::pluck('id')->toArray();
        $this->rewardType = RewardType::pluck('id')->toArray();
        $this->cash = RewardType::where('type', 'Cash')->first();
        $this->point = RewardType::where('type', 'Points')->first();
        parent::__construct();

    }
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

        $cashData = (isset($this->cash)) ? $this->cash->id : '';
        $pointData = (isset($this->point)) ? $this->point->id : '';
        // dd($pointData);
        return [
            'campaignTypeId' => ['required', 'numeric', Rule::in($this->campainType)],
            'rewardSplitId' => ['required', 'numeric', Rule::in($this->rewardSplit)],
            'rewardTypeId' => ['required', 'numeric', Rule::in($this->rewardType)],
            'campaignImages'=>['required'],
            'campaignImages.*'=>['required', 'is_base64_image'],
            'name' => ['required', 'string'],
            'fullAmount' => ['required_if:campaignTypeId,==,'. $cashData],
            'fullPoints' => ['required_if:campaignTypeId,==,'.$pointData],
            'description' => ['required'],
            'cashPerPerson' => ['required_if:campaignTypeId,==,'. $cashData, 'string'],
            'pointPerPerson' => ['required_if:campaignTypeId,==,'.$pointData, 'string'],
            'campaignCode' => ['required'],
            'referralAllocationForAmbassador' => ['nullable'],
            'referralAllocationForNormalUser' => ['nullable'],
            'startDate' => ['required', 'date_format:Y-m-d'],
            'endDate' => ['required', 'date_format:Y-m-d'],
            'running' => ['sometimes','nullable']
        ];
    }

    public function messages()
    {
        return [
            'campaignTypeId.required' => "Please include a campaign type(".implode(',',$this->campainType).") from the campaign type endpoint",
            'rewardSplitId.required' => "Please include a reward split type(".implode(',',$this->rewardSplit).") from the reward split endpoint",
            'rewardTypeId.required' => "Please include a reward type(Cash-".$this->cash->id." or Point-".$this->point->id.") from the reward type endpoint",
            'campaignImages.required' => 'Please include campaign posters to be shown on the poynts app',
            'campaignImages.*.is_base64_image' => 'Campaign posters must be a compatible base64 encoded string'
        ];
    }
}
