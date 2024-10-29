<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

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

function arrayKeyToSnakeCase(array $data): array
{
    foreach ($data as $key => $datum) {
        $data[Str::snake($key)] = $datum;
    }

    return $data;
}

function jsonHttpHeaders(): array
{
    return [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ];
}

function formatModelName(string $modelName = 'User'): ?string
{
    if (!empty($modelName) && class_exists($class = 'App\\Models\\' . ucfirst($modelName))) return $class;
    return null;
}
