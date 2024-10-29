<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RewardSplit extends Model
{
    use HasFactory;

    protected $fillable = [
        'split_type',
        'short_code'
    ];
}
