<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class StoreCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_category(): void
    {
        $auth = Http::post('auth/api/user/login', [
            'username' => 'kweku',
            'password' => 'password',
            'deviceOs' => 'ios'
        ]);

        $user = User::query()->create([
            'external_id' => $auth->json('data.externalId')
        ]);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $auth->json('data.bearer.token')])
            ->post('/api/categories',[
            'name' => 'Phones & Accessories'
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('categories', [
            'name' => 'Phones & Accessories',
        ]);

    }
}
