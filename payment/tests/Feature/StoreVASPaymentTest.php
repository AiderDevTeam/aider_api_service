<?php

namespace Tests\Feature;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class StoreVASPaymentTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    /**
     * Test successful VAS payment creation.
     *
     * @return void
     */
    public function testSuccessfulVASPaymentCreation(): void
    {
//        $user
        $this->refreshDatabase();

        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjQwMDAvYXBpL3VzZXIvbG9naW4iLCJpYXQiOjE2OTIyODQ3MDksImV4cCI6MTcwODA1MjcwOSwibmJmIjoxNjkyMjg0NzA5LCJqdGkiOiJhS2FlQVZrTktkWGc5b3pKIiwic3ViIjoiMSIsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjciLCJleHRlcm5hbElkIjoiZXhJRCJ9._ciy2IupJYJMn7rg-xwPxI2LJKulYKII7oZHU61-XQ0';
        $userExternalId = 'J0207932004M';
        $phone = '0207932004';

        $response = $this->withToken($token)->post('api/vas-payments/create', [
            'userExternalId' => $userExternalId,
            'type' => 'data bundle',
            'bundleValue' => '25MB',
            'wallets' => [
                'collection' => [
                    'accountName' => 'Nii Attram Mensah',
                    'accountNumber' => $phone,
                    'sortCode' => 'vod',
                    'externalId' => uniqid(),
                ],
                'disbursement' => [
                    'accountName' => 'Nii Attram Mensah',
                    'accountNumber' => $phone,
                    'sortCode' => 'vod',
                ],
            ],
            'amount' => 100.00,
        ]);

        logger($response->content());

        $this->assertDatabaseHas('v_a_s_payments', [
            'description' => '25MB',
            'type' => 'data bundle'
        ]);

        $this->assertDatabaseHas('transactions', [
            'account_number' => '0207932004',
            'r_switch' => 'vod',
        ]);



        $this->refreshDatabase();
    }
}
