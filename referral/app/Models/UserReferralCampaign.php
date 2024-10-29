<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserReferralCampaign extends Model
{
    use HasFactory;

    public $fillable = [
        'user_id',
        'campaign_id',
        'referral_no',
        'referral_url'
    ];

    public function user():BelongsTo{
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
