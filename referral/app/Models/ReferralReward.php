<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralReward extends Model
{
    use HasFactory;

    protected $fillable =[
        'external_id',
        'referral_id',
        'reward_status',
        'reward_value',
        'referrer_account_number',
        'referrer_account_number_sort_code'
    ];


    public function referral(): BelongsTo
    {
        return $this->belongsTo(Referral::class);
    }
}
