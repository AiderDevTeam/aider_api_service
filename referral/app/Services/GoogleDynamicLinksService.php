<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GoogleDynamicLinksService{

    public static  function getDynamicLink($code, $campaignId, $localUser){
        logger('### GENERATE DYNAMIC LINK:::'.$code);
        try{
            $user = json_decode($localUser->user_details);
        }catch(\Exception $e){
            logger('### ERROR OCURRED WHILE GETING USER DETAILS:::');
            report($e);
        }
        $dynamicLinkRequest = Http::post(env('FIREBASE_DYNAMIC_LINKS_URL')."?key=".env('FIREBASE_API_KEY'), [
            "dynamicLinkInfo"=> [
                "domainUriPrefix"=> env('FIREBASE_DYNAMIC_LINKS_DOMAIN'),
                "link"=> env('FIREBASE_DYNAMIC_LINKS_DOMAIN')."?code=$code&campaignId=$campaignId",
                "androidInfo"=> [
                  "androidPackageName"=> env('ANDROID_PACKAGE_NAME')
                ],
                "iosInfo"=> [
                  "iosBundleId"=> env("IOS_PACKAGE_NAME"),
                  'iosAppStoreId' => env('FIREBASE_DYNAMIC_LINK_APPLE_ID')
                ],
                'socialMetaTagInfo' => [
                    'socialTitle' => (!empty($user)) ? $user->firstName." ".$user->lastName." is inviting you to Poynt!" : "Get on Poynt!",
                    'socialDescription' =>"Hi, I have an exclusive Black Ticket for the POYNT app and I want to give it to you! Click on the link to join for exclusive deals👀🔥", //env("SOCIAL_DESCRIPTION"),
                    'socialImageLink' => env("SOCIAL_IMAGE")
                ]
            ]
        ]
        );

        logger([
            "dynamicLinkInfo"=> [
                "domainUriPrefix"=> env('FIREBASE_DYNAMIC_LINKS_DOMAIN'),
                "link"=> env('FIREBASE_DYNAMIC_LINKS_DOMAIN')."?code=$code&campaignId=$campaignId",
                "androidInfo"=> [
                  "androidPackageName"=> env('ANDROID_PACKAGE_NAME')
                ],
                "iosInfo"=> [
                  "iosBundleId"=> env("IOS_PACKAGE_NAME"),
                  'iosAppStoreId' => env('FIREBASE_DYNAMIC_LINK_APPLE_ID')
                ],
                'socialMetaTagInfo' => [
                    'socialTitle' => (!empty($user)) ? $user->firstName." ".$user->lastName." is inviting you to Poynt!" : "Get on Poynt!",
                    'socialDescription' =>"Hi, I have an exclusive Black Ticket for the POYNT app and I want to give it to you! Click on the link to join for exclusive deals👀🔥", //env("SOCIAL_DESCRIPTION"),
                    'socialImageLink' => env("SOCIAL_IMAGE")
                ]
            ]
        ]);
        
        $response = $dynamicLinkRequest->json();
        logger('### DYNAMIC LINK RESPONSE:::'.$code);
        logger($response);
        if($dynamicLinkRequest->successful() && isset($response['shortLink'])){
            return  ['shortLink' => $response['shortLink']];
        }
    }
}
