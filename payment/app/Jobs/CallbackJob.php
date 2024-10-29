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
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger('### CALLBACK JOB STARTED ###');
        logger($this->data);
        logger($this->url);

        logger('### RESPONSE FROM CALLBACK ###');
        logger(Http::withHeaders(jsonHttpHeaders())->post($this->url, $this->data));
    }
}
