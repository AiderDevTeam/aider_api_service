<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Http\Resources\UserResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use AppierSign\RealtimeModel\Traits\RealtimeModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, RealtimeModel;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
       'external_id',
       'points',
       'user_details',
       'user_type',
       'referral_no'
    ];


    public function referrals():HasMany{
        return $this->hasMany(Referral::class, 'referrer_id', 'external_id'); 
    }

    public function user_referral_number(): HasOne{
        return $this->hasOne(ReferralUserNumber::class, 'external_id', 'external_id');
    }

    public function user_referral_campaign(): HasMany{
        return $this->hasMany(UserReferralCampaign::class, 'user_id', 'id');
    }

    public function getSyncKey(): string
    {
        return 'external_id';
    }

    public function toRealtimeData(): array
    {
       return [
            // "id"=> $this->id,
            // "externalId"=> $this->external_id,
            // "points"=> $this->points,
            // "userDetails"=> json_decode($this->user_details),
            // "createdAt"=> $this->created_at,
            // "updatedAt"=> $this->updated_at,
            // "userType"=> $this->user_type,
            "referralsGiven"=> $this->user_referral_number->referrals_given ?? "0",
            "referralLinks" => $this->user_referral_campaign()->selectRaw('user_referral_campaigns.campaign_id AS campaignId, user_referral_campaigns.referral_url AS referralUrl')->get()

        ];
    }
}

