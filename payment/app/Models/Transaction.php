<?php

namespace App\Models;

use App\Enum\Status;
use App\Http\Services\PaymentService;
use App\Traits\RunsCustomQueries;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory, RunsCustomQueries;

    const TYPES = [
        'COLLECTION' => 'collection',
        'DISBURSEMENT' => 'disbursement',
        'REVERSAL' => 'reversal'
    ];

    protected $fillable = [
        'external_id',
        'user_id',
        'payment_id',
        'amount',
        'account_number',
        'status',
        'stan',
        'sort_code',
        'bank_code',
        'recipient_code',
        'type',
        'callback_url',
    ];

    public function getRouteKeyName(): string
    {
        return 'external_id';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function date(): string
    {
        if (is_array($this) || !$this->exists) return '';

        return ($this->created_at->isToday()) ? 'Today-' . $this->created_at->format('h:iA') : $this->created_at->format('dS M, Y-h:iA');
    }

    public function failed(): bool
    {
        return $this->update(['status' => Status::FAILED->value]);
    }

    public function completed(): bool
    {
        return $this->update(['status' => Status::COMPLETED->value]);
    }

    public function started(): bool
    {
        return $this->update(['status' => Status::STARTED->value]);
    }

    public function reversed(): bool
    {
        return $this->update(['status' => Status::REVERSED->value]);
    }

    public static function getByExternalId(string $externalId): object|null
    {
        return Transaction::query()->where('external_id', '=', $externalId)->first();
    }


    public function getSyncKey(): string
    {
        return 'external_id';
    }

    public function toRealtimeData(): array
    {
        return [];
    }

    public function isDisbursement(): bool
    {
        return $this->type === self::TYPES['DISBURSEMENT'];
    }

    public function process(): void
    {
        if ($this->isDisbursement()) $this->initializeDisbursement();
    }

    private function initializeDisbursement(): void
    {
        PaymentService::disburse($this);
    }
}

