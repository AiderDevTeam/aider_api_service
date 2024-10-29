<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_logout()
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

        $this->post('api/user/logout',[],
            ['Authorization' => 'Bearer' . JWTAuth::fromUser($user)]
        )->assertStatus(204);
    }
}
