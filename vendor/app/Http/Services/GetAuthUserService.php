<?php

namespace App\Http\Services;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class GetAuthUserService
{
    public static function getUser($externalId): ?array
    {
        logger('### FETCHING USER DETAILS FROM AUTH-SERVICE ###');
        logger($url = "/auth/api/sys/get-user/$externalId");

        $response = Http::get($url);
        return $response->successful() ? $response->json()['data'] : null;
    }

    public static function checkUsernameExistence(string $username): bool
    {
        try {
            logger('### SENDING CHECK USERNAME EXISTENCE REQUEST TO AUTH SERVICE ###');
            logger($data = ['username' => $username]);

            $request = Http::post('http://auth/api/sys/username-check', $data);
            logger($request->json());

            return $request->successful();

        } catch (Exception $exception) {
            report($exception);
        }
        return false;
    }
}
