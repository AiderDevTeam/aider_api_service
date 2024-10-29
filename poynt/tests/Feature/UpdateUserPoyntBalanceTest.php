<?php

namespace Tests\Feature;

use App\Models\ActionPoynt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UpdateUserPoyntBalanceTest extends TestCase
{
    public function test_credit_user_poynt_balance(): void
    {
        ActionPoynt::query()->updateOrCreate(['action' => 'airtime purchase'],
            ['action' => 'airtime purchase', 'poynt' => 10, 'type' => 'value']);

        $user = Http::post('auth/api/user/login', ['username' => 'karece', 'password' => 'password'])->json();

        $response = $this->withHeaders(
            ['Authorization' => 'Bearer ' . $user['data']['bearer']['token']]
        )->post('api/user/update-poynt-balance', [
            'type' => 'credit',
            'action' => 'airtime purchase',
            'actionResponsePayload' => ["kyc approval response payload"],
            'actionValue' => 60
        ]);
        $response->assertJson(['data' => ['poyntBalance' => 600]]);
        $response->assertStatus(200);
    }

    public function test_debit_user_poynt_balance(): void
    {
        $user = Http::post('auth/api/user/login', ['username' => 'karece', 'password' => 'password'])->json();

        $response = $this->withHeaders(
            ['Authorization' => 'Bearer ' . $user['data']['bearer']['token']]
        )->post('api/user/update-poynt-balance', [
            'type' => 'debit',
            'actionResponsePayload' => ["kyc approval response payload"],
            'debitPoynt' => 200
        ]);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJson(['data' => ['poyntBalance' => 400]]);
    }
}
