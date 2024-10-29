<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SignupTest extends TestCase
{
    use RefreshDatabase;

    public function test_signup()
    {
        $requestData = [
            'firstName' => 'Kweku',
            'lastName' => 'Hammond',
            'username' => 'kweku',
            'email' => 'hammondkweku@gmail.com',
            'birthday' => '',
            'gender' => 'male',
            'phone' => '0501376828',
            'callingCode' => '+233',
            'profile' => '',
            'pushNotificationToken' => '',
            'referralCode' => '',
            'referralUrl' => '',
            'deviceOs' => 'android',
            'termsAndConditions' => true,
            'password' => 'password'
        ];
        $response = $this->post('api/user', $requestData);
        $response->assertStatus(201);
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure([
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
