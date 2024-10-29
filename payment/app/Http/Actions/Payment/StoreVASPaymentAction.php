<?php

namespace App\Http\Actions\Payment;

use App\Http\Requests\Payment\StoreVASPaymentRequest;
use App\Http\Resources\TransactionResource;
use App\Jobs\CollectionJob;
use App\Models\VASDiscount;
use App\Models\VASPayment;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreVASPaymentAction
{

    public function handle(Request $authRequest, StoreVASPaymentRequest $vasRequest): JsonResponse
    {
        try {
            if ($vasRequest->validated()) {

                $user = getOrCreateUser($authRequest->user['externalId']);
                $type = $vasRequest->type;

                $airtimeDiscount = VASDiscount::findByType(VASPayment::AIRTIME_TOP_UP)?->discount;
                $dataBundleDiscount = VASDiscount::findByType(VASPayment::DATA_BUNDLE_PURCHASE)?->discount;

                $vasAmount = match ($type) {
                    VASPayment::AIRTIME_TOP_UP => $vasRequest->amount,
                    VASPayment::DATA_BUNDLE_PURCHASE => $vasRequest->amount - ($vasRequest->amount * ($dataBundleDiscount ?? env('DATA_BUNDLE_DISCOUNT_PERCENTAGE')))
                };

                $vasValue = match ($type) {
                    VASPayment::AIRTIME_TOP_UP => $vasRequest->amount + ($vasRequest->amount * ($airtimeDiscount ?? env('AIRTIME_DISCOUNT_PERCENTAGE'))),
                    VASPayment::DATA_BUNDLE_PURCHASE => $vasRequest->amount
                };

                $collectionWallet = $vasRequest->wallets['collection'];
                $disbursementWallet = $vasRequest->wallets['disbursement'];

                $vasPaymentData = [
                    'description' => $vasRequest->bundleValue ?? '',
                    'value' => $vasValue,
                    'type' => $type
                ];

                DB::beginTransaction();

                try {
                    $vasPayment = $user->vasPayments()->create($vasPaymentData);
                    logger('Destination Sort Code', [self::getDestinationSortCode($disbursementWallet['accountNumber'])]);

                    $paymentData = [
                        'collection_account_name' => $collectionWallet['accountName'],
                        'collection_account_number' => $collectionWallet['accountNumber'],
                        'collection_sort_code' => $collectionWallet['sortCode'],
                        'destination_account_number' => $disbursementWallet['accountNumber'],
                        'destination_sort_code' => self::getDestinationSortCode($disbursementWallet['accountNumber']),
                        'destination_account_name' => $disbursementWallet['accountName'],
                        'user_id' => $user->id,
                        'source_external_id' => $collectionWallet['externalId'],
                        'amount' => $vasAmount,
                        'type' => $vasRequest->type,
                    ];

                    $payment = $vasPayment->payment()->create($paymentData);

                    $transactionData = [
                        'user_id' => $user->id,
                        'amount' => $vasAmount,
                        'account_number' => $collectionWallet['accountNumber'],
                        'r_switch' => $collectionWallet['sortCode'],
                    ];

                    $transaction = $payment->transaction()->create($transactionData);

                    DB::commit();

                    $transaction->refresh();

                    CollectionJob::dispatchSync($payment);

                    return successfulJsonResponse(new TransactionResource($transaction));


                } catch (Exception $exception) {
                    DB::rollBack();

                    report($exception);

                    return errorJsonResponse();
                }
            }
        } catch (Exception $exception) {
            report($exception);
        }

        return errorJsonResponse();
    }

    public static function getDestinationSortCode(string $destinationNumber): string
    {
        return match (substr($destinationNumber, 0, 3)) {
            '020', '050' => 'VOD',
            '026', '056', '057', '027' => 'ATL',
            default => 'MTN'
        };
    }

}
