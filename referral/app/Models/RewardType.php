<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RewardType extends Model
{
    use HasFactory;
    
    public $fillable = [
        'type'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
