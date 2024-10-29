<?php

namespace App\Models;

use App\Enum\Status;
use App\Events\FailedDisbursementEvent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{
    use HasFactory;

    const COLLECTION = 'collection';
    const DISBURSEMENT = 'disbursement';
    const REVERSAL = 'reversal';

    const VAS_PAYMENT = 'App\Models\VASPayment';
    const DELIVERY_PAYMENT = 'App\Models\DeliveryPayment';

    const MOMO_DISBURSEMENT = 'momo disbursement';
    const AIRTIME_DISBURSEMENT = 'airtime disbursement';
    const DATA_BUNDLE_DISBURSEMENT = 'data bundle disbursement';

    const DISBURSEMENT_TYPES = [
        self::MOMO_DISBURSEMENT,
        self::AIRTIME_DISBURSEMENT,
        self::DATA_BUNDLE_DISBURSEMENT,
        self::REVERSAL
    ];

    protected $fillable = [
        'user_id',
        'external_id',
        'collection_amount',
        'collection_status',
        'collection_account_number',
        'collection_account_sort_code',
        'disbursement_amount',
        'disbursement_status',
        'disbursement_account_number',
        'disbursement_account_sort_code',
        'reversal_status',
        'paymentable_type',
        'paymentable_id',
    ];

    public function getRouteKeyName(): string
    {
        return 'external_id';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function paymentable(): MorphTo
    {
        return $this->morphTo('paymentable', 'paymentable_type');
    }

    public function failTransaction(): bool
    {
        if ($this->disbursement_tries === 4 && $this->disbursement_status === Status::FAILED->value) {
            event(new FailedDisbursementEvent($this));
            return true;
        };
        return false;
    }

    public function process()
    {
        $this->transaction()->create([
            'amount' => $this->amount,
            'user_id' => $this->user->id,
        ]);

//        CollectionJob::dispatchSync($this->transaction);
    }

    public function setReversingStatus(): bool
    {
        return $this->update(['reversal_status' => Status::REVERSING->value]);
    }

    public function incrementReversalTries(): bool
    {
        $number = $this->reversal_tries + 1;
        return $this->update(['reversal_tries' => $number]);
    }

    public function completed(): bool
    {
        return $this->transaction->update(['status' => Status::COMPLETED->value]);
    }

    public function failed(): void
    {
        $this->transaction->update(['status' => Status::FAILED->value]);
    }

    public function isDelivery(): bool
    {
        return $this->paymentable_type == DeliveryPayment::class;
    }

    public function isVAS(): bool
    {
        return $this->paymentable_type == VASPayment::class;
    }

    public function isBooking(): bool
    {
        return $this->paymentable_type === BookingPayment::class;
    }

}
