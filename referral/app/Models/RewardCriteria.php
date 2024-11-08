<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RewardCriteria extends Model
{
    use HasFactory;
    protected $table='reward_criteria';
    
    protected $fillable = [
        'criteria',
        'short_code'
    ];
}
