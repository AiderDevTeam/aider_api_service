<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UpdateUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_update()
    {
        $user = User::create([
            'first_name' => 'Kweku',
            'last_name' => 'Hammond',
            'username' => 'kweku',
            'email' => 'hammondkweku@gmail.com',
            'birthday' => "",
            'gender' => 'male',
            'phone' => '0501376828',
            'calling_code' => '+233',
            'profile_photo_url' => '',
            'push_notification_token' => '',
            'referral_code' => '',
            'referral_url' => '',
            'device_os' => 'android',
            'terms_and_conditions' => true,
            'password' => 'password'
        ]);

        $response = $this->put('api/user', ['birthday' => '1996-03-06'], ['Authorization' => 'Bearer' . JWTAuth::fromUser($user)]);
        $response->assertStatus(204);
    }
}
