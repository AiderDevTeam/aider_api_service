<?php

namespace App\Http\Services;

use App\Models\BankAccount;
use App\Models\PaystackPayment;
use App\Models\Transaction;
use Exception;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class PaystackService
{
    public function __construct(private readonly Transaction $transaction)
    {
    }

    private static function makeGetRequest(string $url): PromiseInterface|Response
    {
        $response = Http::withHeaders(['Authorization' => 'Bearer ' . env('PAY_STACK_SECRET_KEY')])
            ->get($url);

        logger('### API RESPONSE ###');
        logger($response);

        return $response;
    }

    private static function makePostRequest(string $url, array $payload): PromiseInterface|Response
    {
        $response = Http::withHeaders(['Authorization' => 'Bearer ' . env('PAY_STACK_SECRET_KEY')])
            ->post($url, $payload);

        logger('### API RESPONSE ###');
        logger($response);

        return $response;
    }

    private function logTransaction(Transaction $transaction, array $data): Model|Builder
    {
        return PaystackPayment::query()->create([
            'transaction_id' => $transaction->id,
            'request_payload' => $data
        ]);
    }


    public function disburseToBank()
    {
        logger('### DISPATCHING BANK DISBURSEMENT REQUEST TO PAYSTACK ###');
        try {
            logger($url = env('PAY_STACK_BASE_URL') . '/transfer');
            logger($requestPayload = [
                'source' => 'balance',
                'reason' => $this->transaction->description,
                'amount' => $this->transaction->amount,
                'recipient' => $this->transaction->recipient_code,
                'reference' => $this->transaction->stan,
            ]);

            $paymentLog = $this->logTransaction($this->transaction, $requestPayload);

            $response = self::makePostRequest($url, $requestPayload);

            $paymentLog->update([
                'response_payload' => [$response->json()]
            ]);

            if ($response->successful()) {
                return $response->json()['data'];
            }

        } catch (Exception $exception) {
            report($exception);
        }
        return null;
    }

    public static function createTransferRecipient(array $requestPayload)
    {
        logger('### DISPATCHING ADD TRANSFER RECIPIENT REQUEST TO PAYSTACK ###');
        try {
            logger($url = env('PAY_STACK_BASE_URL') . '/transferrecipient');

            $response = self::makePostRequest($url, [
                'type' => $requestPayload['type'],
                'name' => $requestPayload['name'],
                'account_number' => $requestPayload['accountNumber'],
                'bank_code' => $requestPayload['bankCode'],
                'currency' => $requestPayload['currency']
            ]);

            if ($response->successful())
                return $response->json();

        } catch (Exception $exception) {
            report($exception);
        }
        return null;
    }

    public static function verifyTransaction(string $reference)
    {
        logger('### DISPATCHING TRANSACTION VERIFICATION REQUEST TO PAYSTACK ###');
        logger($url = env('PAY_STACK_BASE_URL') . '/transaction/verify/' . $reference);
        try {
            $response = self::makeGetRequest($url);
            if ($response->successful())
                return $response->json();

        } catch (Exception $exception) {
            report($exception);
        }
        return null;
    }

    public static function listBanks(string $country = 'nigeria')
    {
        logger('### DISPATCHING GET BANKS LIST REQUEST TO PAYSTACK ###');
        logger($url = env('PAY_STACK_BASE_URL') . "/bank?use_cursor=false&country=$country");
        try {

            $response = self::makeGetRequest($url);
            if ($response->successful())
                return $response->json();

        } catch (Exception $exception) {
            report($exception);
        }
        return null;
    }

    public static function resolveAccount(string $accountNumber, string $bankCode): Model|Builder|null
    {
        logger('### DISPATCHING ACCOUNT NUMBER RESOLUTION REQUEST TO PAYSTACK ###');
        logger($url = env('PAY_STACK_BASE_URL') . "/bank/resolve?account_number=$accountNumber&bank_code=$bankCode");
        try {

            $response = self::makeGetRequest($url);

            if ($response->successful()) {
                $data = $response->json()['data'];

                return BankAccount::query()->updateOrCreate(
                    ['account_number' => $data['account_number']],
                    ['account_name' => $data['account_name'], 'bank_code' => $bankCode]
                );
            }
        } catch (Exception $exception) {
            report($exception);
        }
        return null;
    }
}
