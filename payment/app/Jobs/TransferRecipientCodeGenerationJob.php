<?php

namespace App\Jobs;

use App\Models\Wallet;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class TransferRecipientCodeGenerationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private Wallet $wallet)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger()->info('### TRANSFER RECIPIENT CODE GENERATION JOB STARTED ###');
        logger($data = [
            'type' => 'nuban',
            'name' => $this->wallet->account_name,
            'accountNumber' => $this->wallet->account_number,
            'bankCode' => $this->wallet->bank_code,
            'currency' => 'NGN',
        ]);

        try {
            $response = Http::post(env('API_GATEWAY_BASE_URL') . '/api/store-transfer-recipient', $data);
            logger('### RESPONSE FROM API-GATEWAY ###', [$response]);

            if (!$response->successful() && !isset($response->json()['data']['recipientCode'])) {
                logger()->error('### TRANSFER RECIPIENT CODE GENERATION FAILED ###');
                return;
            }

            $this->wallet->updateQuietly(['recipient_code' => $response->json()['data']['recipientCode']]);

            manuallySyncModels([$this->wallet->user]);

        } catch (Exception $exception) {
            report($exception);
        }
        logger()->info('### TRANSFER RECIPIENT CODE GENERATION JOB COMPLETED ###');
    }
}
