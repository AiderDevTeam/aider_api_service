<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Campaign extends Model
{
    use HasFactory;
    protected $fillable = [
        'campaign_type_id',
        'reward_split_id',
        'reward_type_id',
        'name',
        'description',
        'start_date',
        'end_date',
        'cash_per_person',
        'poynt_per_person',
        'campaign_code',
        'running'
    ];


    public function campaign_type():HasOne{
        return $this->hasOne(CampaignType::class, 'id', 'campaign_type_id');
    }

    public function reward_split():HasOne{
        return $this->hasOne(RewardSplit::class, 'id', 'reward_split_id');
    }

    public function reward_type():HasOne{
        return $this->hasOne(RewardType::class, 'id', 'reward_type_id');
    }

    public function reward_value():HasOne{
        return $this->hasOne(RewardValue::class, 'campaign_id', 'id');
    }

    public function referral_allocation():HasOne{
        return $this->hasOne(ReferralAllocation::class, 'campaign_id', 'id');
    }
}
