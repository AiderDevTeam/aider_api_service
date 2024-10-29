<?php

namespace App\Jobs;

use App\Enum\Status;
use App\Models\Payment;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class HandleFailedCollectionCallbackJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger('### JOB TO HANDLE FAILED COLLECTION CALLBACK -- STARTED ###');

        try {
            Payment::query()->where('disbursement_status', '=', Status::PENDING->value)
                ->where('collection_status', '=', Status::PENDING->value)
                ->whereBetween('created_at', [\Carbon\Carbon::yesterday()->toDateString().' 00:00:00',\Carbon\Carbon::now()->toDateString().' 23:59:59'])
                ->chunk(5, function ($payments) {
                    foreach ($payments as $payment) {

//                        logger('### PAYMENT DATA ###', [$payment]);

                        logger('### FETCHING CORRESPONDING COLLECTION TRANSACTION FROM API-GATEWAY ###');
                        logger($url = 'http://api-gateway/api/get-collection-transaction/' . $payment->transaction->external_id);
                        $response = Http::get($url);
//                        logger('### RESPONSE FROM API-GATEWAY ###', [$response]);

                        if ($response->successful()) {
                            $transactionData = $response->json()['data'];
//                            logger('### API-GATEWAY COLLECTION TRANSACTION DATA ### ', [$transactionData]);

                            $request = Http::post('http://payment/webhooks/payment-collection-callback', $transactionData);

//                            logger('### WEBHOOK RESPONSE ###', [$request]);
                        }
                    }
                });
        } catch (Exception $exception) {
            report($exception);
        }
        logger('### JOB TO HANDLE FAILED COLLECTION CALLBACK -- COMPLETED ###');
    }
}
