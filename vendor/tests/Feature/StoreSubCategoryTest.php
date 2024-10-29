<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class StoreSubCategoryTest extends TestCase
{
    use RefreshDatabase;
    public function test_store_sub_category(): void
    {
        $auth = Http::post('auth/api/user/login', [
            'username' => 'kweku',
            'password' => 'password',
            'deviceOs' => 'ios'
        ]);

        $user = User::query()->create([
            'external_id' => $auth->json('data.externalId')
        ]);

        $category = Category::query()->create(['name' => 'Phones & Accessories']);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $auth->json('data.bearer.token')])
            ->post('/api/sub-category', [
                'name' => 'shirt',
                'categoryId' => $category->id
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('sub_categories', [
            'name' => 'shirt',
        ]);
    }

}
