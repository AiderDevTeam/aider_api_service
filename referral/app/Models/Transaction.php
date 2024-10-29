<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;
    public $fillable=[
        'stan',
        'referrer_id',
        'referred_id',
        'amount',
        'response_code',
        'response_message',
        'full_request',
        'full_response',
        'has_performed_transaction',
        'campaign_id'
    ];

    public function referred(): BelongsTo{
        return $this->belongsTo(User::class, 'referred_id', 'id');
    }

    public function referrer(): BelongsTo{
        return $this->belongsTo(User::class, 'referrer_id', 'id');
    }

    public function campaign(): BelongsTo{
        return $this->belongsTo(Campaign::class);
    }
}
