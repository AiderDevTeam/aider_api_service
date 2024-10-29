<?php

use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

function user(string $guard): Authenticatable
{
    return auth($guard)->user();
}

function successfulJsonResponse(mixed $data = [], string $message = 'Request processed successfully', $statusCode = 200): JsonResponse
{
    return response()->json([
        'success' => true,
        'message' => $message,
        'data' => $data
    ], $statusCode);
}

function errorJsonResponse(array $errors = [], string $message = 'Something went wrong, please try again later', $statusCode = 500): JsonResponse
{
    return response()->json([
        'success' => false,
        'message' => $message,
        'errors' => $errors
    ], $statusCode);
}

function toSnakeCase($data)
{
    foreach ($data as $key => $value) {
        $data[Str::snake($key)] = $value;
    }

    if (isset($data['userExternalId']) && $user = User::getByExternalId($data['userExternalId'])) $data['user_id'] = $user->id;
    return $data;
}

function arrayKeyToSnakeCase(array $data): array
{
    $newData = [];
    foreach ($data as $key => $datum) {
        $newData[Str::snake($key)] = $datum;
    }
    return $newData;
}

function getOrCreateUser(string $externalId): ?User
{
    logger("EXTERNAL ID  OF USER is " . $externalId);
    $user = User::firstOrCreate(['external_id' => $externalId]);
    logger($user);

    return $user;
}

function getPaymentByTransactionExternalId(string $externalId): Model|Builder|null
{
    return Transaction::query()->where('external_id', '=', $externalId)->first()?->payment;
}

function getTransactionExternalIdByPayment(Payment $payment): string
{
    return $payment->transaction->external_id;
}

function setPayoutWallet(object $wallet): object|bool
{
    Wallet::query()->where('user_id', $wallet->user_id)->update(['payout' => false]);
    $wallet->payout = true;
    $wallet->save();
    return $wallet;
}

function toFloat($amount): float
{
    return stripCommas(
        number_format($amount, 2)
    );
}

function stripCommas(string $value): array|string
{
    return str_replace(',', '', $value);
}

function formatForLogging(?array $data): string
{
    $toString = "";
    if ($data) {
        foreach ($data as $key => $value)
            $toString .= (is_array($value)) ? "$key:\n" . formatForLogging($value) : "$key: $value\n";
    }
    return $toString;
}

function jsonHttpHeaders(): array
{
    return [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ];
}

function manuallySyncModels(array $models): void
{
    if ($models) {
        foreach ($models as $model) {
            logger()->info('### SYNCING MODEL: ' . class_basename($model) . ' ###');
            $model->syncData($model->external_id);
        }
    }
}

function getDestinationSortCode(string $destinationNumber): string
{
    return match (substr($destinationNumber, 0, 3)) {
        '020', '050' => 'VOD',
        '026', '056', '057', '027' => 'ATL',
        default => 'MTN'
    };
}

function generateStan(): string
{
    return now()->format('YmdHisu');
}
