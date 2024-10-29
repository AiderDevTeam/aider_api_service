<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'order_number',
        'accepted_by',
        'rejected_reason',
        'reschedule'
    ];
}
