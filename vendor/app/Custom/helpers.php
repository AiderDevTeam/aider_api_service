<?php

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

const COLORBOX = [
    'MAIN_SHOP' => 'ColorboxCosmetics',
    'PERSONAL_SHOP' => 'StephanieAdu',
];

const SHOP_LOGO_FILE_TYPES = [
    'BASE64_STRING' => 'base64-string',
    'URL' => 'url',
    'FORM_DATA_FILE' => 'form-data-file'
];

function successfulJsonResponse(mixed $data, string $message = 'Request processed successfully', $statusCode = 200): JsonResponse
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

function paginatedSuccessfulJsonResponse($data = [], $message = 'Request processed successfully', int $statusCode = 200): JsonResponse
{

    $responseData = $data->response()->getData();
    $metaData = $responseData->meta;
    $linksData = $responseData->links;

    foreach ($metaData->links as $link) {
        $link->url = formatUrl($link->url);
    }

    return response()->json([
        'success' => true,
        'message' => $message,
        'data' => $data,
        'meta' => [
            'first' => formatUrl($linksData->first),
            'last' => formatUrl($linksData->last),
            'prev' => formatUrl($linksData->prev),
            'next' => formatUrl($linksData->next),
            'currentPage' => $metaData->current_page,
            'perPage' => $metaData->per_page,
            'total' => $metaData->total,
            'links' => $metaData->links,
        ]
    ], $statusCode);
}

function formatUrl($url): ?string
{
    if (is_null($url))
        return null;

    $parts = parse_url($url);

    $port = isset($parts['port']) ? ':' . $parts['port'] : '';
    $path = $parts['path'] ?? '';
    $path = '/vendor' . rtrim($path, '/');
    $query = isset($parts['query']) ? '?' . $parts['query'] : '';
    return $parts['scheme'] . '://' . $parts['host'] . $port . $path . $query;
}

;

function arrayKeyToSnakeCase(array $data): array
{
    $newData = [];
    foreach ($data as $key => $datum) {
        $newData[Str::snake($key)] = $datum;
    }
    return $newData;
}

function arrayKeyToCamelCase(array $data): array
{
    $newData = [];
    foreach ($data as $key => $datum) {
        $newData[Str::camel($key)] = $datum;
    }
    return $newData;
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
    foreach ($models as $model) {
        logger()->info('### SYNCING MODEL: ' . class_basename($model) . ' ###');
        $model->syncData($model->external_id);
    }
}

function formatModelName(string $modelName = 'User'): ?string
{
    if (!empty($modelName) && class_exists($class = 'App\\Models\\' . ucfirst($modelName))) return $class;
    return null;
}

function setCartUniqueId(User $user)
{
    if ($lastCart = $user->carts()->where('is_checked_out', '=', false)
        ->whereNull('deleted_at')
        ->whereNotNull('unique_id')
        ->latest()->first())
        return $lastCart->unique_id;

    return uniqid('CART');
}

function generateOrderNumber(): string
{
    return 'P' . Carbon::now()->format('ymdHis');
}

function generateBookingNumber(): string
{
    return 'A' . Carbon::now()->format('ymdHis');
}

function getLocalPath($file)
{
    return $file->store('public/uploads');
}

function deleteLocalFile(string $path): void
{
    if (Storage::exists($path)) {
        Storage::delete($path);
    }
}
