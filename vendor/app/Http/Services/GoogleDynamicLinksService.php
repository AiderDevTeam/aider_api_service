<?php

namespace App\Http\Services;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class GoogleDynamicLinksService
{
    public static function generateLink(string $additionalLinkData, array $socialMetaTagInfo): ?array
    {
        //eg: $additionalLinkData = "?externalId=K0501376828H"
        try {

            logger('### GENERATING DYNAMIC LINK ###');
            logger($socialMetaTagInfo);
            $request = Http::post(env('FIREBASE_DYNAMIC_LINKS_URL') . "?key=" . env('FIREBASE_API_KEY'), [
                    "dynamicLinkInfo" => [
                        "domainUriPrefix" => env('FIREBASE_DYNAMIC_LINKS_DOMAIN'),
                        "link" => env('FIREBASE_DYNAMIC_LINKS_DOMAIN') . $additionalLinkData,
                        "androidInfo" => [
                            "androidPackageName" => env('ANDROID_PACKAGE_NAME')
                        ],
                        "iosInfo" => [
                            "iosBundleId" => env("IOS_PACKAGE_NAME"),
                            'iosAppStoreId' => env('FIREBASE_DYNAMIC_LINK_APPLE_ID')
                        ],
                        'socialMetaTagInfo' => [
                            'socialTitle' => $socialMetaTagInfo['title'],
                            'socialDescription' => $socialMetaTagInfo['description'],
                            'socialImageLink' => $socialMetaTagInfo['shareImage']
                        ]
                    ]
                ]
            );
            logger('### RESPONSE FROM GOOGLE DYNAMIC SERVICE ###');
            logger($request);

            if ($request->successful() && isset($request->json()['shortLink']))
                return ['link' => $request->json()['shortLink']];

        } catch (Exception $exception) {
            report($exception);
        }
        return null;
    }
}
