<?php

namespace App\Jobs;

use App\Models\Bank;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class LoadBanksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger('### LOADING BANKS JOB RUNNING');

        try {
            $response = Http::withHeaders(jsonHttpHeaders())->get('http://api-gateway/api/list-banks');
            if ($response->successful()) {
                $banks = $response->json()['data'];

                logger('### NUMBER OF BANKS:', [count($banks)]);
                foreach ($banks as $bank) {
                    Bank::query()->updateOrCreate([
                        'sort_code' => $bank['slug']
                    ], [
                        'name' => $bank['name'],
                        'bank_code' => $bank['code'],
                        'long_code' => $bank['longcode'],
                        'country' => $bank['country'],
                        'currency' => $bank['currency']
                    ]);

                    sleep(1);
                }
            }

        } catch (Exception $exception) {
            report($exception);
        }

    }
}
