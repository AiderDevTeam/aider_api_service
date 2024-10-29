<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id',
        'account_name',
        'account_number',
        'bank_code'
    ];

    public static function getAccountByNumber(string $accountNumber)
    {
        return self::where('account_number', '=', $accountNumber)->first();
    }

}
