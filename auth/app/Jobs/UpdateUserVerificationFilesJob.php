<?php

namespace App\Jobs;

use App\Http\Services\API\FileUploadService;
use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateUserVerificationFilesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $userExternalId;
    /**
     * Create a new job instance.
     */
    public function __construct(public User $user, public array $data)
    {
        //
        $this->userExternalId = $this->user->external_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger()->info('### UPDATE USER VERIFICATION FILES JOB DISPATCHED ###'.$this->userExternalId);
        try {
            $files = $this->uploadFiles();
            logger("### USER VERIFICATION RESPONSE IDPHOTOURL($this->userExternalId):". $files['idPhoto']);
            logger("### USER VERIFICATION RESPONSE SELFIEURL($this->userExternalId):". $files['selfie']);
            $this->user->update([
                'id_photo_url' => $files['idPhoto'] ?? "",
                'selfie_url' => $files['selfie'] ?? ""
            ]);
            logger()->info('### USER VERIFICATION FILES UPDATED ##');
        } catch (Exception $exception) {
            report($exception);
            logger("### USER GENERAL VERIFICATION ERROR($this->userExternalId)::::".json_encode($exception->getMessage()));
        }
    }

    private function uploadFiles(): array
    {
        $files = $this->data;
        $uploadedFiles = [];
        foreach (['idPhoto', 'selfie'] as $fileKey) {
            try{
                $uploadedFiles[$fileKey] = FileUploadService::upload($files[$fileKey], true)['url'];
                logger("### USER VERIFICATION RESPONSE($this->userExternalId)::::".  $uploadedFiles[$fileKey]);
            }catch(\Exception $e){
                logger("### USER VERIFICATION UPLOAD EXCEPTION ERROR($this->userExternalId)::::". json_encode($e->getMessage()));
            }
           

        }
        return $uploadedFiles;
    }
}
