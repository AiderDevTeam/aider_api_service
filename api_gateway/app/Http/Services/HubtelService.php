<?php

namespace App\Http\Services;

use App\Models\HubtelPayment;
use App\Models\Transaction;
use Exception;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class HubtelService
{
    public function __construct(private readonly Transaction $transaction)
    {
    }

    private static function getAccountNumber(string $accountNumber): string
    {
        return '233' . substr($accountNumber, -9);
    }

    private static function getChannelName(string $network): string
    {
        return match (strtolower($network)) {
            'mtn' => 'mtn-gh',
            'vodafone', 'vod', 'vdf' => 'vodafone-gh',
            'atl', 'tgo', 'airteltigo' => 'tigo-gh'
        };
    }

    private function logTransaction(Transaction $transaction, array $data): Model|Builder
    {
        return HubtelPayment::query()->create([
            'transaction_id' => $transaction->id,
            'request_payload' => $data
        ]);
    }

    private function getApiCredentials(): array
    {
        return ['apiId' => env('HUBTEL_API_ID'), 'apiKey' => env('HUBTEL_API_KEY')];
    }

    private function post(string $url, array $data): PromiseInterface|Response
    {
        $credentials = $this->getApiCredentials();
        return Http::withBasicAuth(
            $credentials['apiId'],
            $credentials['apiKey']
        )->post($url, $data);
    }

    private static function getAirtimeServiceId(string $network)
    {
        return match (strtolower($network)) {
            'vodafone', 'vod', 'vdf' => env('VODAFONE_AIRTIME_SERVICE_ID'),
            'atl', 'tgo', 'airteltigo' => env('AIRTELTIGO_AIRTIME_SERVICE_ID'),
            default => env('MTN_AIRTIME_SERVICE_ID')
        };
    }

    private static function getDataBundleServiceId(string $network)
    {
        return match (strtolower($network)) {
            'vodafone', 'vod', 'vdf' => env('VODAFONE_DATA_BUNDLE_SERVICE_ID'),
            'atl', 'tgo', 'airteltigo', 'air' => env('AIRTELTIGO_DATA_BUNDLE_SERVICE_ID'),
            default => env('MTN_DATA_BUNDLE_SERVICE_ID')
        };
    }

    public function collect()
    {
        $transaction = $this->transaction;
        try {
            $requestPayload = [
                'CustomerName' => 'Customer NAME',
                'CustomerMsisdn' => self::getAccountNumber($transaction->account_number),
                'CustomerEmail' => 'engineering@itspoynt.com',
                'Channel' => self::getChannelName($transaction->r_switch),
                'Amount' => $transaction->amount,
                'PrimaryCallbackUrl' => env('HUBTEL_WEBHOOK'),
                'Description' => $transaction->description,
                'ClientReference' => $transaction->stan
            ];
            $paymentLog = $this->logTransaction(transaction: $this->transaction, data: $requestPayload);

            logger()->info('### HUBTEL COLLECTION REQUEST ###');
            logger()->info($url = env('HUBTEL_DIRECT_RECEIVE_API_BASE_URL') .
                '/' .
                env('HUBTEL_COLLECTION_MERCHANT_ID') .
                '/receive/mobilemoney');
            logger($requestPayload);

            $response = $this->post($url, $requestPayload);
            logger()->info('### HUBTEL COLLECTION RESPONSE ###');
            logger($response->json());
            $paymentLog->update([
                'response_payload' => [$response->json()]
            ]);
            return $response->json();

        } catch (Exception $exception) {
            report($exception);
        }
        return null;
    }

    public function disburseMomo(){
        $transaction = $this->transaction;
        try {
            $data = [
                'RecipientName' => 'Customer NAME',
                'RecipientMsisdn' => self::getAccountNumber($transaction->account_number),
                'RecipientEmail' => 'engineering@itspoynt.com',
                'Channel' => self::getChannelName($transaction->r_switch),
                'Amount' => $transaction->amount,
                'PrimaryCallbackUrl' => env('HUBTEL_WEBHOOK'),
                'Description' => $transaction->description,
                'ClientReference' => $transaction->stan
            ];
            $payment = $this->logTransaction(transaction: $transaction, data: $data);
            info('### HUBTEL DISBURSEMENT REQUEST ###');
            info($url = env('HUBTEL_DIRECT_SEND_API_BASE_URL') . '/' . env('HUBTEL_DISBURSEMENT_MERCHANT_ID'). '/send/mobilemoney');
            logger($data);
            $request = $this->post($url, $data);
            info('### HUBTEL MOMO DISBURSEMENT RESPONSE ###');
            info($request->json());
            $payment->update([
                'response_payload' => [$request->json()]
            ]);
            return $request->json();
        } catch (Exception $exception) {
            report($exception);
        }
        return null;

    }

    public function disburseAirtime()
    {
        try {
            logger()->info('### AIRTIME TOP UP ###');

            logger()->info($url = env('HUBTEL_COMMISSION_SERVICE_BASEURL')
                . '/' . env('HUBTEL_DISBURSEMENT_MERCHANT_ID')
                . '/' . self::getAirtimeServiceId($this->transaction->r_switch));

            $credentials = $this->getApiCredentials();

            $requestPayload = [
                'Destination' => $this->transaction->account_number,
                'Amount' => $this->transaction->amount,
                'CallbackUrl' => env('HUBTEL_WEBHOOK'),
                'ClientReference' => $this->transaction->stan,
            ];
            $this->logTransaction($this->transaction, $requestPayload);
            $request = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($credentials['apiId'] . ':' . $credentials['apiKey']),
                'Cache-Control' => 'no-cache'
            ])->post($url, $requestPayload);

            logger('### AIRTIME TOP UP RESPONSE ###');
            logger($request->json());
            return $request->json();

        } catch (Exception $exception) {
            report($exception);
        }
        return null;
    }

    public function disburseDataBundle()
    {
        try {
            logger()->info("### DATA BUNDLE TOP UP ###");

            logger()->info($url = env('HUBTEL_COMMISSION_SERVICE_BASEURL')
                . '/' . env('HUBTEL_DISBURSEMENT_MERCHANT_ID')
                . '/' . self::getDataBundleServiceId($this->transaction->r_switch));

            $requestPayload = [
                'Destination' => $this->transaction->account_number,
                'Amount' => $this->transaction->amount,
                'CallbackUrl' => env('HUBTEL_WEBHOOK'),
                'ClientReference' => $this->transaction->stan,
                'Extradata' => [
                    'bundle' => $this->transaction->description
                ],
            ];

            $this->logTransaction($this->transaction, $requestPayload);

            $credentials = $this->getApiCredentials();

            $request = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($credentials['apiId'] . ':' . $credentials['apiKey']),
                'Cache-Control' => 'no-cache'
            ])->post($url, $requestPayload);

            logger('### BUNDLE TOP UP RESPONSE ###');
            logger($request->json());
            return $request->json();
        } catch (Exception $exception) {
            report($exception);
        }
        return null;
    }

    public static function getDataBundlePackages(string $destinationNumber, string $network): bool|PromiseInterface|Response
    {
        try {
            logger()->info('### GETTING BUNDLE PACKAGES AVAILABLE ###');
            logger()->info($url = env('HUBTEL_COMMISSION_SERVICE_BASEURL')
                . '/' . env('HUBTEL_DISBURSEMENT_MERCHANT_ID')
                . '/' . self::getDataBundleServiceId($network)
                . '?destination=' . $destinationNumber);

            logger('API ID ::: ', [env('HUBTEL_API_ID')]);
            logger('API KEY ::: ', [env('HUBTEL_API_KEY')]);

            return Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode(env('HUBTEL_API_ID') . ':' . env('HUBTEL_API_KEY')),
                'Cache-Control' => 'no-cache'
            ])->get($url);

        } catch (Exception $exception) {
            report($exception);
        }
        return false;
    }
}
