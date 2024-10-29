<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'transaction_id',
        'account_number',
        'amount',
        'status',
        'message',
        'callback_url',
        'description',
        'stan',
        'code',
        'switch_code',
        'success',
        'reason',
    ];

}
