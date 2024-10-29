<?php

namespace App\Listeners;

use App\Events\ScoreUserPoyntAfterTransactionEvent;
use App\Http\Resources\TransactionResource;
use App\Http\Services\PoyntService;
use App\Models\VASPayment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ScoreUserPoyntAfterTransactionListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ScoreUserPoyntAfterTransactionEvent $event): void
    {
        $transaction = $event->transaction;
        $action = match ($transaction->payment->type) {
            VASPayment::AIRTIME_TOP_UP => 'airtime purchase',
            VASPayment::DATA_BUNDLE_PURCHASE => 'data bundle purchase',
            default => null
        };
        if (!is_null($action)) {
            PoyntService::scorePoynt(
                'credit',
                $action,
                [new TransactionResource($transaction)],
                $transaction->payment->amount,
                $transaction->user->external_id
            );
        }

    }
}
