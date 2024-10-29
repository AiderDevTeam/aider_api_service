<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncIndividualModelsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public array $modelExternalIds, public string $modelName)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger('### MANUALLY SYNC INDIVIDUAL MODELS STARTED ###');
        try {
            $records = (new ($this->modelName))::whereIn('external_id', $this->modelExternalIds)->get();

            if ($records->count() > 0) {
                foreach($records as $record){
                    $record->syncData($record->{$record->getSyncKey()});
                }
            }
        } catch (Exception $exception) {
            report($exception);
        }
        logger('### MANUALLY SYNC INDIVIDUAL MODELS COMPLETED ###');
    }
}
