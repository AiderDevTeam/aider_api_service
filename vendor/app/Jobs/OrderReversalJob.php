<?php

namespace App\Jobs;

use App\Custom\Status;
use App\Models\Order;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class OrderReversalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

    private Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger()->info('### ORDER REVERSAL JOB STARTED ###');
        try {
            $response = Http::withHeaders(jsonHttpHeaders())->post('http://payment/webhooks/payment-delivery-callback', [
                'deliveryExternalId' => $this->order->delivery->external_id,
                'deliverySuccessful' => false,
                'disbursementAmount' => $this->order->amount_paid,
                'disbursementCallbackUrl' => 'http://vendor/webhooks/disbursement-callback-response'
            ]);
            if ($response->successful()) $this->order->updateQuietly(['reversal_status' => Status::PENDING]);

            logger($response);
        } catch (Exception $exception) {
            report($exception);
        }
        logger()->info('### ORDER REVERSAL JOB COMPLETED ###');
    }
}
