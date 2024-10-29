<?php

namespace Tests\Feature;

use App\Enum\Status;
use App\Http\Services\PaymentService;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use App\Models\VASPayment;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful VAS payment creation and collection payment processing.
     *
     * @test
     */
    public function testSuccessfulCollection(): void
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

        $transaction = Transaction::latest()->first();
        $payment = $transaction->payment;

        $data = [
            'transactionId' => $transaction->external_id,
            'amount' => $transaction->amount,
            'rSwitch' => $transaction->r_switch,
            'accountNumber' => $transaction->account_number,
            'description' => 'payment',
            'callbackUrl' => 'http://example.com/callback',
            'type' => Payment::COLLECTION,
        ];


        $paymentService = new PaymentService($data);
        $paymentService->handle();

        $this->assertEquals(Status::PENDING->value, $payment->collection_status);

    }
}
