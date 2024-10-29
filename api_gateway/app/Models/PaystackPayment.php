<?php

namespace App\Models;

use App\Http\Services\PaystackService;
use App\Interfaces\PaymentProcessorInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaystackPayment extends Model
{
    use HasFactory;

    protected $table = 'paystack_payment_logs';

    protected $fillable = [
        'transaction_id',
        'request_payload',
        'response_payload'
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function setRequestPayloadAttribute(array $data): void
    {
        $this->attributes['request_payload'] = json_encode($data);
    }

    public function setResponsePayloadAttribute(array $data): void
    {
        $this->attributes['response_payload'] = json_encode($data);
    }

    public function getRequestPayloadAttribute()
    {
        return json_decode($this->attributes['request_payload'], true);
    }

    public function getResponsePayloadAttribute()
    {
        return json_decode($this->attributes['response_payload'], true);
    }

    public function disburseToBank(Transaction $transaction): ?Transaction
    {
        $response = (new PaystackService($transaction))->disburseToBank();
        logger($response);
        if (isset($response['status']) && isset($response['transfer_code'])) {
            $transaction->update([
                'status' => $response['status'],
                'response_message' => transactionPendingMessage(),
                'transfer_code' => $response['transfer_code']
            ]);
        } else {
            $transaction->fail();
        }
        return $transaction->refresh();
    }
}
