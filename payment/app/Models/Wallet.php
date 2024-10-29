<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    use HasFactory;

    const BANK = 'bank';
    const MAX_ALLOWED_WALLETS = 6;

    protected $fillable = [
        'external_id',
        'user_id',
        'account_name',
        'account_number',
        'type',
        'sort_code',
        'bank_code',
        'recipient_code',
        'default',
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getRouteKeyName(): string
    {
        return 'external_id';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function setAsDefault(): bool
    {
        return $this->update(['default' => true]);
    }

    public static function getByExternalId(string $externalId): object|null
    {
        return Wallet::query()->where('external_id', '=', $externalId)->first();
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'bank_code', 'bank_code');
    }

}
