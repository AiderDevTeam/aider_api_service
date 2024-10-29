<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralAllocation extends Model
{
    use HasFactory;
    protected $table="referral_allocations";
    protected $primaryKey = "id";
    protected $fillable = [
        'campaign_id',
        'ambassador',
        'user'
    ];

        /**
         * The "booting" method of the model.
         *
         * @return void
         */
        protected static function boot()
        {
            parent::boot();
        
            // auto-sets values on creation
            static::creating(function ($query) {
                $query->created_at = Carbon::now()->toDateTimeString();
            });
        }
        
}
