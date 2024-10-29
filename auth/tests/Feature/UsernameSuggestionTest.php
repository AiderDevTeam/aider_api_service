<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UsernameSuggestionTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $requestData = [
            'username' => 'Kumi',
            'firstName' => 'Kweku',
            'lastName' => 'Hammond'
        ];
        $response = $this->post('api/suggest-username',$requestData);
        $response->assertStatus(200);
    }
}
