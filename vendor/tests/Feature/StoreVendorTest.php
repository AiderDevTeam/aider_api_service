<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class StoreVendorTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_vendor()
    {
        $auth = Http::post('auth/api/user/login', [
            'username' => 'kweku',
            'password' => 'password',
            'deviceOs' => 'ios'
        ]);

        $user = User::query()->updateOrCreate([
            'external_id' => $auth->json('data.externalId')
        ]);

        $category1 = Category::query()->create(['name' => 'Fashion']);
        $category2 = Category::query()->create(['name' => 'Thrift']);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $auth->json('data.bearer.token')])
            ->post('/api/store', [
                'userId' => $user->id,
                "shopLogoUrl" => "www.poynt.com",
                "businessName" => "Main business",
                "walletNumber" => "0200327946",
                "sortCode" => "VOD",
                "shopTag" => "its_poynt",
                "city" => "Accra",
                "longitude" => "45791",
                "latitude" => "123454",
                "categoriesIds" => [$category1->id, $category2->id]
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('vendors', [
            'user_id' => $user->id,
            'business_name' => 'Main business',
            'shop_tag' => 'its_poynt',
        ]);

        $this->assertDatabaseHas('addresses', [
            "city" => "Accra",
            "longitude" => "45791",
            "latitude" => "123454"
        ]);

        $response->assertJson(['success' => true]);
    }
}
