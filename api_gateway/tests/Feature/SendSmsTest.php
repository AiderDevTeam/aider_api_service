<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SendSmsTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_send_sms(): void
    {
        $requestData = [
            'to' => '0501376828',
            'message' => 'Hello, running test'
        ];

        $response = $this->post('api/sms', $requestData);
        $response->assertStatus(204);
    }
}
