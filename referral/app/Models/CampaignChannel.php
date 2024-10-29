<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignChannel extends Model
{
    use HasFactory;

    protected $table="campaign_channels";

    protected $fillable=[
        'channel',
        'availability'
    ];
}
