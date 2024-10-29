<?php

namespace App\Http\Actions;

use App\Models\IdVerification;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GetVerificationDataAction
{
    public function handle(Request $request): JsonResponse
    {
        try {
            if (isset($request['idNumber'])) {
                if ($verificationData = IdVerification::where('id_number', '=', $request['idNumber'])->first()) {
                    return successfulJsonResponse(data: [
                        'firstName' => $verificationData->forenames,
                        'lastName' => $verificationData->surname,
                        'birthDate' => $verificationData->birth_date,
                        'gender' => $verificationData->gender
                    ], message: 'data found');
                }
                return errorJsonResponse(errors: ['Id verification data not found'], statusCode: Response::HTTP_NOT_FOUND);
            }
            return errorJsonResponse(errors: ['id number is required'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
