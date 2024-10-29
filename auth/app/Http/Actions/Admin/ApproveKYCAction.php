<?php

namespace App\Http\Actions\Admin;

use App\Custom\Status;
use App\Events\SaveIdVerificationLogEvent;
use App\Http\Requests\KYCApprovalRequest;
use App\Http\Services\API\IdVerificationService;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;

class ApproveKYCAction
{
    public function handle(KYCApprovalRequest $request): JsonResponse
    {
        try {
            logger('### ADMIN KYC APPROVAL STARTED ###');
            logger($request->validated());

            $data = $request->validated();

            $user = User::findWithExternalId($data['userExternalId']);

            $logResponse = $data['verificationStatus'] ===
            Status::REJECTED ? $data['rejectionReason'] : $data['verificationStatus'];

            self::saveVerificationLog($user, [
                'status' => $data['verificationStatus'],
                'response' => $logResponse
            ]);

            $user->update([
                'id_verification_status' => $data['verificationStatus'],
            ]);

            if ($data['verificationStatus'] === Status::APPROVED) {
                self::updateUserDetailsOnApprovedKYC($user);
            }

            return successfulJsonResponse(message: 'KYC ' . $data['verificationStatus']);

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }

    public static function saveVerificationLog(User $user, array $data): void
    {
        $user->idVerificationLogs()->create([
            'external_id' => uniqid(),
            'status' => $data['status'],
            'response' => $data['response'],
        ]);
    }

    public static function updateUserDetailsOnApprovedKYC(User $user): void
    {
        $verificationData = IdVerificationService::getIdVerificationData($user->id_number);
        if (!is_null($verificationData)) {
            $user->update([
                'first_name' => $verificationData['firstName'],
                'last_name' => $verificationData['lastName'],
                'birthday' => $verificationData['birthDate'],
                'gender' => $verificationData['gender'],
                'id_verified' => true,
                'id_verified_at' => now()->toDateTimeString()
            ]);
        }
    }
}
