<?php

namespace App\Models;

use App\Http\Resources\CampaignChannelsResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use AppierSign\RealtimeModel\Traits\RealtimeModel;

class Referral extends Model
{
    use HasFactory, RealtimeModel;

    protected $fillable = [
        'campaign_id',
        'external_id',
        'referrer_id',
        'referred_id',
        'campaign_channel_id',
        'code',
        'referral_link'
    ];

    const REWARD_TYPE = [
        'CASH' => 'cash',
        'POINTS' => 'points'
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function campaign_channel(): HasOne
    {
        return $this->hasOne(CampaignChannel::class, 'id', 'campaign_channel_id');
    }

    public function referred(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_id', 'external_id');
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id', 'external_id');
    }

    public function getSyncKey(): string
    {
        return 'external_id';
    }

    public function toRealtimeData(): array
    {
        return [
            "campaignId" => $this->campaign_id,
            "externalId" => $this->external_id,
            "referrerId" => $this->referrer_id,
            "referredId" => $this->referred_id,
            "referrer" => json_decode($this->referrer->user_details),
            "referred" => json_decode($this->referred->user_details),
            "narration" => json_decode($this->referred->user_details)->firstName. " is now a member because of you " ,
            "campaignChannelId" => $this->campaign_channel_id,
            "referralLink" => $this->referral_link,
            "campaignChannel" => new CampaignChannelsResource($this->campaign_channel),
            "id" => $this->id
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'referrer_id';
    }

    public function collection(): string
    {
        return 'referrals';
    }

    public static function getReferredById(string $referredId)
    {
        return self::where('referred_id', $referredId);
    }

    public function reward(): HasOne
    {
        return $this->hasOne(ReferralReward::class);
    }

    public function getRewardValue()
    {
        return match (strtolower($this->campaign->reward_type->type)) {
            self::REWARD_TYPE['CASH'] => $this->campaign->cash_per_person,
            self::REWARD_TYPE['POINTS'] => $this->campaign->poynt_per_person,
            default => null
        };
    }
}
