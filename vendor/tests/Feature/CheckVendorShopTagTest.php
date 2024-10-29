<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CheckVendorShopTagTest extends TestCase
{
    use RefreshDatabase;
    public function test_check_shop_tag_exists(): void
    {
        $auth = Http::post('auth/api/user/login', [
            'username' => 'kweku',
            'password' => 'password',
            'deviceOs' => 'ios'
        ]);

        $user = User::query()->create([
            'external_id' => $auth->json('data.externalId')
        ]);

        $vendor = $this->withHeaders(['Authorization' => 'Bearer ' . $auth->json('data.bearer.token')])
            ->post('/api/store', [
                'userId' => 1,
                "shopLogoUrl" => "www.poynt.com",
                "businessName" => "Main business",
                "shopTag" => "its_poynt",
                "city" => "Accra",
                "longitude" => "45791",
                "latitude" => "123454"
            ]);

        $response = $this->post('/api/check-shop-tag', [
            'shopTag' => 'its_poynt'
        ]);

        $response->assertStatus(200);
    }
}
