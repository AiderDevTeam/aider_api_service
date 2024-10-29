<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Transaction extends Model
{
    use HasFactory;

    const BANK_DISBURSEMENT = 'bank disbursement';
    const MOMO_COLLECTION = 'momo collection';
    const MOMO_DISBURSEMENT = 'momo disbursement';
    const AIRTIME_DISBURSEMENT = 'airtime disbursement';
    const DATA_BUNDLE_DISBURSEMENT = 'data bundle disbursement';
    const REVERSAL = 'reversal';

    const DISBURSEMENT_TYPES = [
        self::BANK_DISBURSEMENT,
        self::REVERSAL,
    ];

    const PENDING = 'pending';
    const FAILED = 'failed';
    const SUCCESS = 'success';

    protected $fillable = [
        'external_id',
        'amount',
        'stan',
        'r_switch',
        'account_number',
        'recipient_code',
        'transfer_code',
        'status',
        'type',
        'description',
        'response_code',
        'response_message',
        'callback_url',
        'processor_type',
        'processor_id'
    ];

    public function processor(): MorphTo
    {
        return $this->morphTo();
    }

    public function isSuccessful(): bool
    {
        return $this->status === self::SUCCESS;
    }

    public function isBankDisbursement(): bool
    {
        return $this->type === self::BANK_DISBURSEMENT;
    }

    public function isMomoDisbursement(): bool
    {
        return $this->type === self::MOMO_DISBURSEMENT;
    }

    public function isDataBundleDisbursement(): bool
    {
        return $this->type === self::DATA_BUNDLE_DISBURSEMENT;
    }

    public function isAirtimeDisbursement(): bool
    {
        return $this->type === self::AIRTIME_DISBURSEMENT;
    }

    public function isMomoCollection(): bool
    {
        return $this->type === self::MOMO_COLLECTION;
    }

    public function isReversal(): bool
    {
        return $this->type === self::REVERSAL;
    }

    public function processorIs(string $name): bool
    {
        return mb_strtolower($this->processor->name) === mb_strtolower($name);
    }

    public function getRouteKeyName(): string
    {
        return 'stan';
    }

    public static function getByStan(string $stan): Model|Builder|null
    {
        return Transaction::query()->where('stan', '=', $stan)->first();
    }

    public function fail(): bool
    {
        return $this->update(['status' => self::FAILED, 'response_code' => '555']);
    }

    public function success(): bool
    {
        return $this->update(['status' => self::SUCCESS, 'response_code' => '000']);
    }

    public function pending(array $additionalFields = []): bool
    {
        return $this->update([... $additionalFields, 'status' => self::PENDING, 'response_code' => '111']);
    }

    public function process(): void
    {
        if ($this->isBankDisbursement()) $this->disburseBank();
//        if ($this->isMomoCollection()) $this->collect();
//        if ($this->isMomoDisbursement() || $this->isReversal()) $this->disburseMomo();
//        if ($this->isAirtimeDisbursement()) $this->disburseAirtime();
//        if ($this->isDataBundleDisbursement()) $this->disburseDataBundle();
    }

    private function disburseBank(): void
    {
        info("### PROCESSING BANK DISBURSEMENT TRANSACTION ###", ['id' => $this->id, 'amount' => $this->amount, 'stan' => $this->stan]);
        (new $this->processor->name)->disburseToBank($this);
    }

    private function collect(): void
    {
        info("### PROCESSING COLLECTION TRANSACTION ###", ['id' => $this->id, 'amount' => $this->amount, 'stan' => $this->stan]);
        (new $this->processor->name)->collect($this);
    }

    private function disburseAirtime(): void
    {
        info("### PROCESSING AIRTIME DISBURSEMENT ###", ['id' => $this->id, 'amount' => $this->amount, 'stan' => $this->stan]);
        (new $this->processor->name)->disburseAirtime($this);
    }

    private function disburseDataBundle(): void
    {
        info("### PROCESSING DATA BUNDLE DISBURSEMENT ###", ['id' => $this->id, 'amount' => $this->amount, 'stan' => $this->stan]);
        (new $this->processor->name)->disburseDataBundle($this);
    }

    private function disburseMomo(): void
    {
        info("### PROCESSING MOMO DISBURSEMENT TRANSACTION ###", ['id' => $this->id, 'amount' => $this->amount, 'stan' => $this->stan]);
        (new $this->processor->name)->disburseMomo($this);
    }
}
