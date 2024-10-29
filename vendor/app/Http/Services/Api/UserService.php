<?php
namespace App\Http\Services\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UserService
{
    public static function updateType(Request $request)
    {
        $response = Http::withToken($request->bearerToken())->withHeaders(jsonHttpHeaders())->post('auth/api/user/update-usertype',[
            'userType' => ["vendor"]
        ]);
    }

    public static function vote(Request $request)
    {
        $response = Http::withToken($request->bearerToken())->withHeaders(jsonHttpHeaders())->put('auth/api/user',[
            'voted' => true
        ]);
        logger($response->successful());
    }

}
