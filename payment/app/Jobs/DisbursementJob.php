<?php

namespace App\Jobs;

use App\Http\Services\PaymentService;
use App\Models\Payment;
use App\Models\VASPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DisbursementJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public Payment $payment)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */

    public function handle(): void
    {
        $disbursementData = [
            ...$this->getPaymentData(),
            'transactionId' => getTransactionExternalIdByPayment($this->payment),
            'rSwitch' => strtoupper($this->payment->destination_sort_code),
            'accountNumber' => $this->payment->destination_account_number,
            'callbackUrl' => 'http://payment/webhooks/payment-disbursement-callback',
        ];

        $disbursement = new PaymentService($disbursementData);

        $disbursement->handle();
    }

    private function getPaymentData(): array
    {
        logger()->info('### PAYMENT DISBURSEMENT STARTED ###');

        if ($this->payment->paymentable_type === Payment::VAS_PAYMENT) {

            return match ($this->payment->paymentable?->type) {

                VASPayment::DATA_BUNDLE_PURCHASE => [
                    'type' => Payment::DATA_BUNDLE_DISBURSEMENT,
                    'amount' => toFloat(($this->payment->paymentable->value * 100)),
                    'bundleValue' => $this->payment->paymentable->description,
                    'description' => Payment::DATA_BUNDLE_DISBURSEMENT
                ],
                VASPayment::AIRTIME_TOP_UP => [
                    'type' => Payment::AIRTIME_DISBURSEMENT,
                    'amount' => toFloat(($this->payment->paymentable->value * 100)),
                    'description' => Payment::AIRTIME_DISBURSEMENT,
                ],
                default => [
                    'type' => Payment::MOMO_DISBURSEMENT,
                    'description' => Payment::MOMO_DISBURSEMENT,
                    'amount' => toFloat($this->payment->amount * 100)
                ]
            };
        }

        if ($this->payment->paymentable_type === Payment::DELIVERY_PAYMENT) {
            return [
                'type' => Payment::MOMO_DISBURSEMENT,
                'description' => 'Delivery Payout',
                'amount' => toFloat(($this->payment->disbursement_amount * 100))
            ];
        }

        return [
            'type' => Payment::MOMO_DISBURSEMENT,
            'description' => Payment::MOMO_DISBURSEMENT,
            'amount' => toFloat($this->payment->amount * 100)
        ];
    }

}

