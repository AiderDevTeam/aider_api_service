<?php

namespace Tests\Feature;

use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class StoreDeliveryPaymentTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    /**
     * Test successful Delivery payment creation.
     *
     * @return void
     */
    public function testSuccessfulDeliveryPaymentCreation(): void
    {
        $this->refreshDatabase();

        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjQwMDAvYXBpL3VzZXIvbG9naW4iLCJpYXQiOjE2OTIyODQ3MDksImV4cCI6MTcwODA1MjcwOSwibmJmIjoxNjkyMjg0NzA5LCJqdGkiOiJhS2FlQVZrTktkWGc5b3pKIiwic3ViIjoiMSIsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjciLCJleHRlcm5hbElkIjoiZXhJRCJ9._ciy2IupJYJMn7rg-xwPxI2LJKulYKII7oZHU61-XQ0';
        $userExternalId = 'J0207932004M';
        $phone = '0207932004';


        $validData = [
            'accountNumber' => $phone,
            'accountName' => 'Joseph Mensah',
            'type' => Wallet::MOMO,
            'sortCode' => 'vod',
        ];

        $url = '/api/wallets/create';


        $this->withToken($token)->post($url, $validData);



        $this->withToken($token)->post('api/delivery-payments/create', [
            'userExternalId' => $userExternalId,
            'description' => 'Sample delivery description',
            'deliveryExternalId' => 'delivery-123',
            'vendorExternalId' => 'J0207932004M',
            'collectionWallet' => [
                'accountName' => 'Nii Attram Mensah',
                'accountNumber' => $phone,
                'sortCode' => 'vod',
                'externalId' => uniqid(),
            ],
            'amount' => 150.00,
            'callbackUrl' => 'http://example.com/callback',
        ]);

        $this->assertDatabaseHas('delivery_payments', [
            'description' => 'Sample delivery description',
            'delivery_external_id' => 'delivery-123',
        ]);

        $this->assertDatabaseHas('payments', [
            'amount' => 150.00,
        ]);

        $this->assertDatabaseHas('transactions', [
            'amount' => 150.00,
        ]);

        $this->refreshDatabase();
    }
}
