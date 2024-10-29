<?php

namespace App\Http\Controllers;

use App\Http\Actions\Payment\PaymentUpdateAction;
use App\Http\Actions\Payment\TransactionUpdateAction;
use App\Http\Actions\Transaction\CheckTransactionStatusAction;
use App\Http\Actions\Transaction\CollectionTransactionAction;
use App\Http\Actions\Transaction\DisbursementTransactionAction;
use App\Http\Actions\Webhook\CollectionPaymentWebhookAction;
use App\Http\Actions\Webhook\DisbursementPaymentWebhookAction;
use App\Http\Requests\TransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Transaction $transaction): JsonResponse
    {
        return successfulJsonResponse(data: new TransactionResource($transaction));
    }

    public function updateTransaction(Transaction $transaction, Request $request, TransactionUpdateAction $action): JsonResponse
    {
        return $action->handle($transaction, $request);
    }

    public function updatePayment(Payment $payment, Request $request, PaymentUpdateAction $action): JsonResponse
    {
        return $action->handle($payment, $request);
    }

    public function collectionTransaction(Request $request, TransactionRequest $transactionRequest, CollectionTransactionAction $action): JsonResponse
    {
        return $action->handle($request, $transactionRequest);
    }

    public function disbursementTransaction(TransactionRequest $request, DisbursementTransactionAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function collectionPaymentResponse(Request $request, CollectionPaymentWebhookAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function disbursementPaymentResponse(Request $request, DisbursementPaymentWebhookAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function checkTransactionStatus(Request $request, CheckTransactionStatusAction $action): JsonResponse
    {
        return $action->handle($request);
    }
}
