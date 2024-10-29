<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncModelsToFirestoreJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $model)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger("### SYNCING $this->model MODEL TO FIRESTORE JOB STARTED ###");
        logger('### TOTAL RECORDS ###', [(new ($this->model))::query()->count()]);

        (new ($this->model))::query()->chunk(50, function ($models) {
            foreach ($models as $model) {
                $model->syncData($model->{$model->getSyncKey()});
            }
        });

        logger("### SYNCING $this->model MODEL TO FIRESTORE JOB COMPLETED ###");
    }
}
