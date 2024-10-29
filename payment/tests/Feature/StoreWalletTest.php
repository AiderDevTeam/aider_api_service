<?php

namespace Tests\Feature;

use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class StoreWalletTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_store_wallet_request_validation()
    {
        $this->refreshDatabase();


        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjQwMDAvYXBpL3VzZXIvbG9naW4iLCJpYXQiOjE2OTIyODQ3MDksImV4cCI6MTcwODA1MjcwOSwibmJmIjoxNjkyMjg0NzA5LCJqdGkiOiJhS2FlQVZrTktkWGc5b3pKIiwic3ViIjoiMSIsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjciLCJleHRlcm5hbElkIjoiZXhJRCJ9._ciy2IupJYJMn7rg-xwPxI2LJKulYKII7oZHU61-XQ0';
        $phone = '0207932004';

        $validData = [
            'accountNumber' => $phone,
            'accountName' => 'Kris Carter',
            'type' => Wallet::MOMO,
            'sortCode' => 'vod',
        ];

        $url = '/api/wallets/create';


        $response = $this->withToken($token)->post($url, $validData);
        $response->assertStatus(200);

        $this->refreshDatabase();
    }


}
