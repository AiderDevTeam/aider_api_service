<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class StorePollTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_poll(): void
    {
        $authStore = Http::post('auth/api/user', [
            'firstName' => 'Kay',
            'lastName' => 'Amoakoa',
            'username' => 'amoakoa',
            'email' => 'amoakoa@gmail.com',
            'birthday' => '',
            'gender' => 'female',
            'phone' => '0201581267',
            'callingCode' => '+233',
            'profile' => '',
            'pushNotificationToken' => '',
            'referralCode' => '',
            'referralUrl' => '',
            'deviceOs' => 'android',
            'termsAndConditions' => true,
            'password' => 'password'
        ]);

        $auth = Http::post('auth/api/user/login', [
            'username' => 'amoakoa',
            'password' => 'password',
            'deviceOs' => 'android'
        ]);

        $user = User::query()->updateOrCreate([
            'external_id' => $auth->json('data.externalId')
        ]);

        $category1 = Category::query()->create(['name' => 'Phone']);

        $category2 =Category::query()->create(['name' => 'Laptop']);

        $category3 = Category::query()->create(['name' => 'Electronics']);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $auth->json('data.bearer.token')])
            ->post('/api/polls', [
            "categoriesIds" => [$category1->id,$category2->id,$category3->id],
        ]);

        $response->assertStatus(201);
    }
    public function test_user_already_voted_poll(): void
    {
        $auth = Http::post('auth/api/user/login', [
            'username' => 'kweku',
            'password' => 'password',
            'deviceOs' => 'ios'
        ]);

        $user = User::query()->updateOrCreate([
            'external_id' => $auth->json('data.externalId')
        ]);

        $category1 = Category::query()->create(['name' => 'Phone']);

        $category2 =Category::query()->create(['name' => 'Laptop']);

        $category3 = Category::query()->create(['name' => 'Electronics']);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $auth->json('data.bearer.token')])
            ->post('/api/polls', [
            "categoriesIds" => [$category1->id,$category2->id,$category3->id],
        ]);

        $response->assertStatus(500);
    }
}
