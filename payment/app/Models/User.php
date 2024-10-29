<?php

namespace App\Models;

use App\Http\Resources\UserResource;
use AppierSign\RealtimeModel\Traits\RealtimeModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, RealtimeModel;


    protected $fillable = [
        'external_id',
        'details'
    ];

    public function setDetailsAttribute(?array $data): void
    {
        $this->attributes['details'] = json_encode($data);
    }

    public function getDetailsAttribute()
    {
        return json_decode($this->attributes['details'], true);
    }

    public static function authUser(array $authUser): Model|Builder
    {
        return self::query()->updateOrCreate([
            'external_id' => $authUser['externalId'],
        ], [
            'details' => $authUser
        ]);
    }

    public function wallets(): HasMany
    {
        return $this->hasMany(Wallet::class);
    }

    public function defaultWallet(): Model|HasMany|null
    {
        return $this->wallets()->where('default', true)->first();
    }

    public function hasAddedThisAccountNumber(string $accountNumber): bool
    {
        return $this->wallets()->where('account_number', $accountNumber)->exists();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function deliveryPayments(): HasMany
    {
        return $this->hasMany(DeliveryPayment::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function getDefaultWallet(): Model|HasMany|null
    {
        return $this->wallets()
            ->where('default', true)
            ->where('type', Wallet::BANK)
            ->first();
    }

    public function getPayoutWallet(): Model|HasMany|null
    {
        return $this->wallets()
            ->where('payout', true)
            ->where('type', Wallet::BANK)
            ->first();
    }

    public function getReferralPayoutWallet(): Model|HasMany|null
    {
        return $this->getPayoutWallet() ?? $this->getDefaultWallet();
    }


    public static function getByExternalId(string $externalId): object|null
    {
        return User::query()->where('external_id', '=', $externalId)->first();
    }

    public function getSyncKey(): string
    {
        return 'external_id';
    }

    public function toRealtimeData(): UserResource
    {
        return new UserResource($this->load(['wallets']));
    }

}


