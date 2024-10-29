<?php

namespace App\Http\Services;

use App\Models\Incentive;
use Exception;
use Illuminate\Support\Facades\Http;

class IncentiveService
{
    public function __construct(private readonly Incentive $incentive)
    {

    }

    public function sendCashIncentive(): void
    {
        try {
            logger('SENDING INCENTIVE DISBURSEMENT REQUEST TO API-GATEWAY SERVICE ###');
            logger($url = 'http://api-gateway/api/payments/disbursement');

            logger($data = [
                'transactionId' => $this->incentive->external_id,
                'amount' => $this->incentive->amount * 100,
                'rSwitch' => $this->incentive->sort_code,
                'accountNumber' => $this->incentive->account_number,
                'type' => 'momo disbursement',
                'description' => $this->incentive->description,
                'callbackUrl' => 'http://vendor/webhooks/api-gateway-incentive-disbursement-response'
            ]);

            $response = Http::withHeaders(jsonHttpHeaders())->post($url, $data);

            logger('### RESPONSE FROM API-GATEWAY SERVICE ###');
            logger($response);

        } catch (Exception $exception) {
            report($exception);
        }
    }

    public function sendPoyntIncentive()
    {
        //To do --> send poynt reward request to poynt service
    }
}
