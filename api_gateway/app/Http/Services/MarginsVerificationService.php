<?php

namespace App\Http\Services;


use App\Events\UpdateOrCreateIdVerificationEvent;
use App\Http\Requests\IdVerificationRequest;
use App\Models\IdVerification;
use Exception;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class MarginsVerificationService
{
    public function __construct(public IdVerificationRequest $request)
    {
    }

    public function verifyId(): JsonResponse
    {
        try {
            if (($idVerification = IdVerification::firstWhere('id_number', $this->request->idNumber)) &&
                !($this->request->has('reVerifyId') && $this->request->reVerifyId)) {
                logger('### ID VERIFICATION SUCCESSFUL ###');
                return successfulJsonResponse(
                    data: [
                        'firstName' => $idVerification->forenames,
                        'lastName' => $idVerification->surname,
                        'birthday' => $idVerification->birth_date,
                        'gender' => $idVerification->gender,
                        'signatureUrl' => $idVerification->signature_url,
                        'photoOnIdUrl' => $idVerification->photo_on_id_url,
                        'idVerifiedAt' => now()->toDateTimeString(),
                    ],
                    message: 'Verification Successful'
                );
            }

            $marginsResponse = $this->sendRequestToMargins();

            if ($marginsResponse->successful() && ($response = $marginsResponse->json())
                && isset($response['code']) && $response['code'] === "00") {

                $photoOnIdFile = $this->getFileUrl($response['data']['person']['biometricFeed']['face']['data']);
                $signatureFile = $this->getFileUrl($response['data']['person']['binaries'][0]['data']);

                $message = ($this->request->has('reVerifyId') && $this->request->reVerifyId)
                    ? 'Reverification Successful'
                    : 'Verification Successful';

                $userDetails = $response['data']['person'];

                if (event(new UpdateOrCreateIdVerificationEvent([
                    ...$this->request->validated(),
                    'details' => $response,
                    'signatureUrl' => $signatureFile->url,
                    'photoOnIdUrl' => $photoOnIdFile->url
                ], $idVerification))) {
                    logger('### ID VERIFICATION SUCCESSFUL ###');
                    return successfulJsonResponse(
                        data: [
                            'firstName' => $userDetails['forenames'],
                            'lastName' => $userDetails['surname'],
                            'birthday' => $userDetails['birthDate'],
                            'gender' => $userDetails['gender'],
                            'signatureUrl' => $signatureFile->url,
                            'photoOnIdUrl' => $photoOnIdFile->url,
                            'idVerifiedAt' => now()->toDateTimeString(),
                        ], message: $message);
                }
            }
            return self::handleFailedVerificationResponse($marginsResponse->json());
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse(errors: ['Verification failed. Please try again later.']);
    }

    public function sendRequestToMargins(): PromiseInterface|\Illuminate\Http\Client\Response
    {
        logger('### DISPATCHING ID VERIFICATION REQUEST TO MARGINS ###');
        $request = Http::withHeaders(jsonHttpHeaders())
            ->post(
                env("MARGINS_BASE_URL"),
                [
                    'pinNumber' => $this->request->idNumber,
                    'image' => $this->request->file,
                    "merchantKey" => env('MARGINS_MERCHANT_KEY'),
                    "center" => env('MARGINS_VERIFICATION_CENTER'),
                    "dataType" => 'PNG'
                ]
            );

        logger()->info('### VERIFICATION RESPONSE RECEIVED ###');
        logger()->info($request->json());
        return $request;
    }

    public function getFileUrl(string $baseString): Model|Builder
    {
        return base64ToCloudStorage($baseString, Str::uuid() . 'jpg');
    }

    public static function handleFailedVerificationResponse(array $response): JsonResponse
    {
        if (isset($response['code']) && $response['code'] == "01" &&
            isset($response['data']) && !filter_var($response['data']['verified'], FILTER_VALIDATE_BOOLEAN))
            return errorJsonResponse(
                errors: ['Invalid Ghana card number. Please try again.'],
                message: 'Verification failed',
                statusCode: ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);

        if (isset($response['code']) && $response['code'] == "01" && isset($response['msg']))
            return errorJsonResponse(
                errors: [$response['msg']],
                message: 'Verification failed',
                statusCode: ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);

        return errorJsonResponse(
            errors: ['Verification failed. Please try again later.'],
            statusCode: ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
    }
}
