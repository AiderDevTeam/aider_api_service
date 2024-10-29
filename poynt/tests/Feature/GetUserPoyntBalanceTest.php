<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GetUserPoyntBalanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_user_poynt_balance(): void
    {
        User::query()->updateOrCreate(
            ['external_id' => 'K0501376828H']
        );

        $user = Http::post('auth/api/user/login', ['username' => 'kweku', 'password' => 'password'])->json();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $user['data']['bearer']['token']])->get('api/user/poynt-balance');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure([
            "success",
            "message",
            "data" => [
                "poyntBalance"
            ]
        ]);
    }
}
