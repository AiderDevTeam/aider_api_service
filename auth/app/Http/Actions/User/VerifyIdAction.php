<?php

namespace App\Http\Actions\User;

use App\Custom\Status;
use App\Events\SaveIdVerificationLogEvent;
use App\Http\Requests\VerifyIdRequest;
use App\Http\Services\API\IdVerificationService;
use App\Jobs\UpdateUserVerificationFilesJob;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class VerifyIdAction
{
    public function handle(VerifyIdRequest $request): JsonResponse
    {
        logger()->info("### USER ID VERIFICATION STARTED ###");
        logger('### PROCESSOR ###', [config('app.aliases.idVerificationProcessor')]);

        return match (config('app.aliases.idVerificationProcessor')) {
            IdVerificationService::VERIFICATION_PROCESSOR['HUBTEL'] => self::hubtelIdVerification($request),
            IdVerificationService::VERIFICATION_PROCESSOR['MARGINS'] => self::marginsIdVerification($request),
            default => errorJsonResponse(message: 'no id verification processor found')
        };
    }

    public static function hubtelIdVerification(VerifyIdRequest $request): JsonResponse
    {
        try {
            $user = auth()->user();

            $response = IdVerificationService::verify([
                'idNumber' => $request->validated('idNumber'),
                'idType' => $request->validated('idType'),
            ]);

            if (isset($response) && $response->successful()) {
                $user->update([
                    'id_verification_status' => Status::PENDING,
                    'id_number' => $request->validated('idNumber')
                ]);

                UpdateUserVerificationFilesJob::dispatch($user, [
                    'idPhoto' => $request->validated('idPhoto'),
                    'selfie' => $request->validated('selfie')
                ])->onQueue('high');

                return successfulJsonResponse('Id verification started. pending processing');
            }

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse(errors: ['Verification failed. Check id number and try again'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public static function marginsIdVerification(VerifyIdRequest $request): JsonResponse
    {
        try {
            $user = auth()->user();

            $response = IdVerificationService::verify([
                'idNumber' => $request->validated('idNumber'),
                'idType' => $request->validated('idType'),
                'reVerifyId' => false,
                'file' => $request->validated('selfie')
            ]);

            $responseData = $response->json();
            if (isset($responseData['success']) && isset($responseData['message'])) {
                event(new SaveIdVerificationLogEvent($user, [
                    'status' => $responseData['success'] ? Status::SUCCESS : Status::FAILED,
                    'response' => $responseData['success'] ? $responseData['message'] : $responseData['errors'][0],
                ]));
            }

            if ($response->successful()) {
                $user->update([
                    'first_name' => $responseData['data']['firstName'],
                    'last_name' => $responseData['data']['lastName'],
                    'birthday' => $responseData['data']['birthday'],
                    'gender' => $responseData['data']['gender'],
                    'id_type' => $request->idType,
                    'id_number' => $request->idNumber,
                    'id_verified' => true,
                    'id_re_verification_status' => false,
                    'photo_on_id_url' => $responseData['data']['photoOnIdUrl'],
                    'signature_url' => $responseData['data']['signatureUrl'],
                    'id_verified_at' => $responseData['data']['idVerifiedAt'],
                ]);

                UpdateUserVerificationFilesJob::dispatch($user,
                    [
                        'idPhoto' => $request->validated('idPhoto'),
                        'selfie' => $request->validated('selfie')
                    ]);

                return successfulJsonResponse('Id Verified');
            }

            if (isset($responseData['message']))
                return errorJsonResponse(
                    errors: $responseData['errors'],
                    message: $responseData['message'],
                    statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
