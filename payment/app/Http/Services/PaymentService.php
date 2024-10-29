<?php

namespace App\Http\Services;

use App\Enum\Status;
use App\Jobs\DisbursementJob;
use App\Models\DeliveryPayment;
use App\Models\Payment;
use App\Models\PaymentLog;
use App\Models\Transaction;
use App\Models\Wallet;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PaymentService
{
    private array $data;
    private string $transactionType;

    public function __construct(array $data = [])
    {

        $this->data = $data;
        if ($data && isset($data['type'])) {
            $this->transactionType = $data['type'];
//            unset($this->data['type']);

//            if ($this->transactionType === Payment::DISBURSEMENT) {
//                $this->data['type'] = $this->data['description'];
//            }
        }

        logger($this->data);
    }

    public function handle(): bool
    {
        $this->logRequest();

        $url = in_array($this->data['type'], Payment::DISBURSEMENT_TYPES) ?
            'api-gateway/api/payments/disbursement' :
            'api-gateway/api/payments/collection';

        logger("### PREPARING TO PROCESS PAYMENT ###", [
            'url' => $url,
            'data' => $this->data
        ]);

        try {
//            if (env('ALLOW_PAYMENT_PROCESSING', true)) {
            $response = Http::withHeaders(jsonHttpHeaders())->post($url, $this->data);

            logger()->info('### PAYMENT RESPONSE ###', $response->json());

            if (isset($response['success']) && ($data = $response['data'])) {
                $this->updateLog([
                    'code' => $data['code'] ?? '',
                    'transaction_id' => $data['transactionId'] ?? '',
                    'stan' => $data['stan'] ?? '',
                ]);

                $payment = getPaymentByTransactionExternalId($this->data['transactionId'])?->first();

                switch ($this->transactionType) {
                    case Payment::COLLECTION:
                        logger()->info('### COLLECTION RESPONSE ###', $data);
                        $payment?->update(['collection_stan' => $data['stan'], 'collection_status' => Status::SUCCESS->value]);
                        break;

                    case Payment::DISBURSEMENT:
                        logger()->info('### DISBURSEMENT RESPONSE ###', $data);
                        $payment?->update(['disbursement_stan' => $data['stan'], 'disbursement_status' => Status::SUCCESS->value]);
                        break;

                    case Payment::REVERSAL:
                        logger()->info('### REVERSAL RESPONSE ###', $data);
                        $payment?->update(['reversal_stan' => $data['stan'], 'reversal_status' => Status::SUCCESS->value]);
                        break;
                }

            }

            if (isset($response['success'])) return true;

//            }
        } catch (Exception $exception) {
            report($exception);
        }

        return false;
    }

    public function checkTransactionStatus($response, $type)
    {
        logger()->info($response);

        if ($response) {
            $code = $response['code'];

            $status = match ($code) {
                '000' => Status::SUCCESS->value,
                '111' => Status::PENDING->value,
                default => Status::FAILED->value
            };


            $this->handlePaymentTransactionStatus($response, $type, $status);


            $this->updateLog([
                'code' => $response['code'] ?? '',
                'transaction_id' => $response['transactionId'] ?? '',
                'stan' => $response['stan'] ?? '',
                'status' => $response['status'] ?? '',
                'message' => $response['responseMessage'] ?? ''
            ]);

            Transaction::getByExternalId($response['transactionId'])->update([
                'response_payload' => json_encode($response['responsePayload'] ?? ''),
            ]);

            return $response;
        }
        return [];
    }

    public function handleDeliveryPaymentDisbursement($response): JsonResponse
    {
        logger()->info($response);

        $deliveryExternalId = $response['deliveryExternalId'] ?? '';
        $vendorUserExternalId = $response['vendorUserExternalId'] ?? '';
        $disbursementAmount = $response['disbursementAmount'] ?? null;
        $disbursementCallbackUrl = $response['disbursementCallbackUrl'] ?? null;

        if (!isset($response['deliverySuccessful'])) {
            logger("Delivery status not provided");
            return errorJsonResponse(["Delivery status not provided"]);
        } else $deliverySuccessful = $response['deliverySuccessful'];

        $wallet = null;
        if ($vendorUserExternalId and $deliverySuccessful) {
            $vendor = getOrCreateUser($vendorUserExternalId);
            $wallet = $vendor->wallets()->where('payout', true)->first();
        }


        $payment = DeliveryPayment::where('delivery_external_id', $deliveryExternalId)->first()?->payment;


        if (!$payment) {
            logger("Payment not found for delivery external ID: $deliveryExternalId");
            return errorJsonResponse(["Payment not found for delivery external ID: $deliveryExternalId"]);
        }


        if ($disbursementAmount && $disbursementCallbackUrl) {
            $payment->update([
                'disbursement_amount' => $disbursementAmount,
                'callback_url' => $disbursementCallbackUrl
            ]);
            $payment->save();
        } else {
            logger("Disbursement amount or callback url not provided");
            return errorJsonResponse(["Disbursement amount or callback url not provided"]);
        }


        if ($deliverySuccessful) {
            if ($wallet) {
                if ($payment->update([
                    'destination_account_number' => $wallet->account_number,
                    'destination_sort_code' => $wallet->sort_code,
                    'destination_account_name' => $wallet->account_name,
                ])) {
                    $payment->save();
                    logger("DESTINATION WALLET UPDATED");
                    logger($payment);
                    DisbursementJob::dispatch($payment);
                    return successfulJsonResponse(message: "Disbursement to vendor started");
                }
            }

            return errorJsonResponse([]);
        }
        DisbursementJob::dispatch($payment);
        return successfulJsonResponse(message: "Refund to buyer started");

    }


    private function logRequest(): void
    {
        $request = [
            'account_number' => $this->data['accountNumber'],
            'amount' => $this->data['amount'],
            'transaction_id' => $this->data['transactionId'],
            'description' => $this->data['description'],
            'callback_url' => $this->data['callbackUrl'] ?? '',
            'switch_code' => $this->data['rSwitch'],
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ];

        logger()->info('### POYNT PROCESS PAYMENT REQUEST ###', [formatForLogging($request)]);

        DB::table('payment_logs')->insert($request);
    }

    public function updateLog(array $data): void
    {
        $log = array_merge($data, [
            'updated_at' => now()->toDateTimeString()
        ]);

        logger()->info('### POYNT PAYMENT REQUEST ###', [formatForLogging($log)]);

        if (isset($data['transaction_id'])) {
            PaymentLog::query()->where('transaction_id', '=', $data['transaction_id'])
                ->latest()
                ->first()
                ->update($log);
        }
    }

    private function handlePaymentTransactionStatus($response, $type, $status): void
    {
        $payment = getPaymentByTransactionExternalId($response['transactionId']);
        logger("### BEFORE UPDATE PAYMENT SERVICE");
        logger([
            'response' => $response,
            'type' => $type,
            'status' => $status
        ]);
        logger($payment);

        if ($type === Payment::COLLECTION) {
            $payment?->update(['collection_status' => $status]);
        }

        if ($type === Payment::DISBURSEMENT) {
            $payment?->update(['disbursement_status' => $status]);
            $payment?->failTransaction();
        }

        if ($type === Payment::REVERSAL) {
            $payment?->update(['reversal_status' => $status]);
        }
        logger("### AFTER UPDATE PAYMENT SERVICE");
        logger($payment?->refresh());
    }

    public static function disburse(Transaction $transaction): void
    {
        try {
            logger()->info('### DISPATCHING DISBURSEMENT REQUEST TO API-GATEWAY SERVICE ###');
            logger($url = env('API_GATEWAY_BASE_URL') . '/api/payments/disbursement');

            logger($payload = [
                'transactionId' => $transaction->external_id,
                'amount' => $transaction->amount * 100,
                'rSwitch' => $transaction->sort_code,
                'accountNumber' => $transaction->account_number,
                'recipientCode' => $transaction->recipient_code,
                'type' => "bank $transaction->type",
                'description' => 'rental payout',
                'callbackUrl' => $transaction->callback_url,
            ]);

            $response = Http::withHeaders(jsonHttpHeaders())->post($url, $payload);

            logger('### RESPONSE FROM API-GATEWAY SERVICE ##');
            logger($response);

        } catch (Exception $exception) {
            report($exception);
        }
    }
}
