<?php

namespace App\Jobs;

use App\Custom\Identification;
use App\Http\Services\API\FileUploadService;
use App\Http\Services\API\PremblyKYCService;
use App\Models\User;
use App\Models\UserIdentification;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class IdentificationVerificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

    private User $user;

    public function __construct(private readonly UserIdentification $userIdentification, private readonly array $data)
    {
        $this->user = $this->userIdentification->user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger()->info('### IDENTIFICATION VERIFICATION JOB --- STARTED ###');

        match (true) {
            $this->userIdentification->isIdNumberWithSelfieVerification() => $this->verifyIdNumberWithSelfie(),
            $this->userIdentification->isDocumentIdentification() => $this->verifyDocument(),
            default => null,
        };

        logger()->info('### IDENTIFICATION VERIFICATION JOB --- COMPLETED ###');
    }

    private function verifyIdNumberWithSelfie(): void
    {
        logger('### PROCESSING ' . $this->userIdentification->formatType() . ' VERIFICATION ###');
        try {
            $selfieUrl = FileUploadService::uploadToImageService($this->data['selfie']);

            $this->userIdentification->updateQuietly(['selfie_url' => $selfieUrl]);

            $response = (new PremblyKYCService([
                'number' => $this->userIdentification->id_number,
                'image' => $selfieUrl,
                'type' => $this->userIdentification->formatType()
            ]))->idNumberWithSelfieVerification();

            if (is_null($response) || !$response->successful()) {
                $this->userIdentification->reject();
                $this->userIdentification->rejectionReasons()->create(['reason' => json_decode($response, true)['errors'][0] ?? 'Verification Failed']);
                return;
            }

            $this->userIdentification->update([
                'status' => Identification::STATUS['ACCEPTED'],
                'selfie_url' => $selfieUrl,
                'verification_details' => $response->json()['data']
            ]);

        } catch (Exception $exception) {
            $this->userIdentification->reject();
            report($exception);
        }
    }

    private function verifyDocument(): void
    {
        logger('### PROCESSING DOCUMENT VERIFICATION ###');

        try {

            $this->userIdentification->updateQuietly([
                'document_url' => FileUploadService::uploadToImageService($this->data['documentImage']),
                'selfie_url' => FileUploadService::uploadToImageService($this->data['selfie'])
            ]);

            $response = (new PremblyKYCService([
                'docType' => $this->userIdentification->formatType(),
                'docCountry' => 'NG',
                'docImage' => $this->data['base64Selfie'],
                'selfieImage' => $this->data['base64DocumentImage']
            ]))->verifyDocument();

            if (is_null($response) || !$response->successful()) {
                $this->userIdentification->reject();
                $this->userIdentification->rejectionReasons()->create(['reason' => json_decode($response, true)['errors'][0] ?? 'Document Verification Failed']);
                return;
            }

            $this->userIdentification->update([
                'status' => Identification::STATUS['ACCEPTED'],
                'verification_details' => $response->json()['data']
            ]);

        } catch (Exception $exception) {
            $this->userIdentification->reject();
            report($exception);
        }
    }

}
