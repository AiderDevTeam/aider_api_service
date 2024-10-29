<?php

namespace App\Http\Controllers;

use App\Http\Actions\CreateTransferRecipientAction;
use App\Http\Actions\GetCollectionTransactionAction;
use App\Http\Actions\GetDisbursementTransactionAction;
use App\Http\Actions\HubtelPaymentWebhookAction;
use App\Http\Actions\PaystackWebhookAction;
use App\Http\Actions\VerifyTransactionAction;
use App\Http\Requests\CreatePaystackTransferRecipientRequest;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function hubtelPaymentWebhookHandler(Request $request, HubtelPaymentWebhookAction $action): ?JsonResponse
    {
        return $action->handle($request);
    }

    public function getTransaction(string $transactionExternalId, GetDisbursementTransactionAction $action): JsonResponse
    {
        return $action->handle($transactionExternalId);
    }

    public function getCollectionTransaction(string $transactionExternalId, GetCollectionTransactionAction $action): JsonResponse
    {
        return $action->handle($transactionExternalId);
    }

    public function verifyTransaction(Request $request, VerifyTransactionAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function createTransferRecipient(CreatePaystackTransferRecipientRequest $request, CreateTransferRecipientAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function paystackWebhookHandler(Request $request, PaystackWebhookAction $action): JsonResponse
    {
        return $action->handle($request);
    }
}
