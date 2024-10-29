<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RewardValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'point',
        'campaign_id',
        'reward_type_id'
    ];


    public function campaign(): BelongsTo{
        return $this->belongsTo(Campaign::class);
    }
}
