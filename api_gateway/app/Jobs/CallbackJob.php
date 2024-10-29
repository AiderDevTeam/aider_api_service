<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class CallbackJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private string $url, private array $data)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger('### CALLBACK JOB STARTED ###');

        logger($this->data);
        $data = json_encode($this->data);
//        $response = Http::post($this->url, $this->data);
        $response = shell_exec("curl --location '$this->url' --header 'Content-Type: application/json' --data '$data'");
        logger()->info("### CALLBACK RESPONSE ###");
        logger($response);

        logger('### CALLBACK JOB COMPLETED ###');
    }
}
