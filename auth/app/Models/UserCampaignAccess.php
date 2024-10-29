<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCampaignAccess extends Model
{
    use HasFactory;

    protected $table = 'user_campaign_accesses';

    protected $fillable = [
        'external_id',
        'user_id',
        'campaign_type'
    ];

    const BLACK_TICKET = 'black_tickets';

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
