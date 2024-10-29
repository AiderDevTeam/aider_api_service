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
    public function __construct(public string $modelName)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger("### SYNCING {$this->modelName} MODEL TO FIRESTORE JOB STARTED ###");
        logger('### TOTAL RECORDS ###', [(new ($this->modelName))::query()->count()]);

        (new ($this->modelName))::query()->chunk(10, function ($models) {
            foreach ($models as $model) {
                $model->syncData($model->{$model->getSyncKey()});
            }
        });

        logger("### SYNCING $this->modelName MODEL TO FIRESTORE JOB COMPLETED ###");
    }
}
