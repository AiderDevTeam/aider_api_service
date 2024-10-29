<?php

namespace App\Models;

use App\Http\Services\HubtelService;
use App\Interfaces\PaymentProcessorInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HubtelPayment extends Model implements PaymentProcessorInterface
{
    use HasFactory;

    protected $table = 'hubtel_payment_logs';

    protected $fillable = [
        'transaction_id',
        'request_payload',
        'response_payload'
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function setRequestPayloadAttribute(array $data)
    {
        $this->attributes['request_payload'] = json_encode($data);
    }

    public function setResponsePayloadAttribute(array $data)
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

    public function disburseMomo(Transaction $transaction): Transaction
    {
        $response = (new HubtelService($transaction))->disburseMomo();
        if (isset($response['ResponseCode']) && $response['ResponseCode'] === '0001') {
            $transaction->pending(['response_message' => transactionPendingMessage()]);
        } else {
            $transaction->fail();
        }
        return $transaction->refresh();
    }

    public function disburseAirtime(Transaction $transaction): Transaction
    {
        $response = (new HubtelService($transaction))->disburseAirtime();
        logger($response);
        if ($response && isset($response['ResponseCode']) && $response['ResponseCode'] === '0001') {
            $transaction->pending(['response_message' => transactionPendingMessage()]);
        } else {
            $transaction->fail();
        }
        return $transaction->refresh();
    }

    public function disburseDataBundle(Transaction $transaction): Transaction
    {
        $response = (new HubtelService($transaction))->disburseDataBundle();
        logger($response);
        if ($response && isset($response['ResponseCode']) && $response['ResponseCode'] === '0001') {
            $transaction->pending(['response_message' => transactionPendingMessage()]);
        } else {
            $transaction->fail();
        }
        return $transaction->refresh();
    }

    public function collect(Transaction $transaction): Transaction
    {
        $response = (new HubtelService($transaction))->collect();
        if ($response && isset($response['ResponseCode']) && $response['ResponseCode'] === '0001') {
            $transaction->pending(['response_message' => transactionPendingMessage()]);
        } else {
            $transaction->fail();
        }
        return $transaction->refresh();
    }

    public function checkStatus(Transaction $transaction): ?Transaction
    {
        // TODO: Implement checkStatus() method.
        return $transaction;
    }
}
