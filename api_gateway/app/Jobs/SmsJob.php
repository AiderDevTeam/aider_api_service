<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected string $phone, protected string $message, protected ?string $from = '')
    {
        //
    }

    protected function sendSms() {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger()->info('### SMS JOB RUNNING ###');
        $this->sendSms();
    }
}
