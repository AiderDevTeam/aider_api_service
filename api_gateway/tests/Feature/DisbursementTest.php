<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DisbursementTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_airtime_disbursement(): void
    {
        $requestData = [
            'transactionId' => '54657238795',
            'amount' => '100',
            'rSwitch' => "VOD",
            'accountNumber' => '0203767186',
            'type' => "airtime disbursement",
            'description' => 'airtime disbursement test',
            'callbackUrl' => 'https://eomksxr76ymgxe0.m.pipedream.net'
        ];

        $response = $this->post('api/payments/disbursement', $requestData);
        $response->assertStatus(202);
    }
}
