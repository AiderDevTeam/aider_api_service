<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\RunCustomQueries;
use AppierSign\RealtimeModel\Traits\RealtimeModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, RunCustomQueries, RealtimeModel;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'external_id',
        'poynt_balance'
    ];

    public function getRouteKeyName(): string
    {
        return 'external_id';
    }

    public function userActionPoynts(): HasMany
    {
        return $this->hasMany(UserActionPoynt::class);
    }

    public function creditPoynt(string $action, array $actionResponsePayload, $actionValue): bool|int
    {
        $actionPoynt = ActionPoynt::where('action', $action)->first();

        if ($actionPoynt) {
            $actionValue = $actionPoynt->isValueBasedAction() ? $actionValue : 1;

            if ($this->userActionPoynts()->create([
                'action_poynt_id' => $actionPoynt->id,
                'poynt' => $actionPoynt->poynt,
                'action_value' => $actionValue,
                'type' => UserActionPoynt::ACTION_POYNT_TYPES['CREDIT'],
                'action_response_payload' => $actionResponsePayload,
            ])) {
                return $this->increment(
                    'poynt_balance',
                    round(($actionPoynt->poynt * $actionValue), 0, PHP_ROUND_HALF_DOWN)
                );
            }
        }
        return false;
    }

    public function debitPoynt(int $debitedPoynt, array $actionResponsePayload): bool|int
    {
        if ($this->userActionPoynts()->create([
            'poynt' => $debitedPoynt,
            'type' => UserActionPoynt::ACTION_POYNT_TYPES['DEBIT'],
            'action_response_payload' => $actionResponsePayload,
        ])) {
            return $this->decrement('poynt_balance', $debitedPoynt);
        }
        return false;
    }

    public function getSyncKey(): string
    {
        return 'external_id';
    }

    public function toRealtimeData(): array
    {
        return [
            'poyntBalance' => $this->poynt_balance,
        ];
    }
}
