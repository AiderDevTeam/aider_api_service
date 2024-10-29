<?php

namespace App\Models;

use App\Http\Resources\BankResource;
use AppierSign\RealtimeModel\Traits\RealtimeModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bank extends Model
{
    use HasFactory, RealtimeModel;

    protected $fillable = [
        'external_id',
        'name',
        'sort_code',
        'bank_code',
        'long_code',
        'country',
        'currency'
    ];

    public function getSyncKey(): string
    {
        return 'external_id';
    }

    public function toRealtimeData(): BankResource
    {
        return new BankResource($this);
    }

    public function wallets(): HasMany
    {
        return $this->hasMany(Wallet::class, 'bank_code', 'bank_code');
    }
}
