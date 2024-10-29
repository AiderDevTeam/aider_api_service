<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralUserNumber extends Model
{
    use HasFactory;
    

    public $fillable = [
        'external_id',
        'referrals_given',
        'campaign_id'

    ];
}
