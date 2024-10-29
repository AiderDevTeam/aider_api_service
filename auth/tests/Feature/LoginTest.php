<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    public function test_login()
    {
        $user = User::create([
            'first_name' => 'Kweku',
            'last_name' => 'Hammond',
            'username' => 'kweku',
            'email' => 'hammondkweku@gmail.com',
            'birthday' => '',
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

        //Test with username
        $usernameTestResponse = $this->post('/api/user/login', [
            'username' => $user->username,
            'password' => 'password',
            'deviceOs' => 'ios',
            'pushNotificationToken' => 'ajslfkjas4wiw82wfsd'
        ]);

        $usernameTestResponse->assertJson(['success' => true]);
        $usernameTestResponse->assertStatus(200)->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'externalId',
                'firstName',
                'lastName',
                'username',
                'email',
                'birthday',
                'gender',
                'phone',
                'profilePhotoUrl',
                'referralCode',
                'referralUrl',
                'termsAndConditions',
                'bearer' => [
                    'token',
                    'expiresIn'
                ]
            ]
        ]);

        //Test with email
        $emailTestResponse = $this->post('/api/user/login', [
            'username' => $user->email,
            'password' => 'password',
            'deviceOs' => 'ios',
            'pushNotificationToken' => 'ajslfkjas4wiw82wfsd'
        ]);

        $emailTestResponse->assertJson(['success' => true]);
        $emailTestResponse->assertStatus(200)->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'externalId',
                'firstName',
                'lastName',
                'username',
                'email',
                'birthday',
                'gender',
                'phone',
                'profilePhotoUrl',
                'referralCode',
                'referralUrl',
                'termsAndConditions',
                'bearer' => [
                    'token',
                    'expiresIn'
                ]
            ]
        ]);
    }

}
