<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UpdateVendorTest extends TestCase
{
    use RefreshDatabase;
    public function test_vendor_update(): void
    {
        $auth = Http::post('auth/api/user/login', [
            'username' => 'hammond',
            'password' => 'password',
            'deviceOs' => 'ios'
        ]);

        $user = User::query()->updateOrCreate([
            'external_id' => $auth->json('data.externalId')
        ]);

        $category1 = Category::query()->create(['name' => 'Fashion']);
        $category2 = Category::query()->create(['name' => 'Thrift']);

        $vendor = Vendor::create([
            "user_id" => $user->id,
            "business_name" => "Main business",
            "shop_tag" => "its_poynt",
            "city" => "Accra",
            "longitude" => "45791",
            "latitude" => "123454",
            "categoriesIds" => [$category1->id, $category2->id]
        ]);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $auth->json('data.bearer.token')])
            ->put("api/vendors/{$vendor->external_id}",[
                'city' => 'Kumasi'
            ]);

        $response->assertStatus(204);
    }
}
