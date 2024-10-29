<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StoreProcessorTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_store_processor(): void
    {
        $requestData = [
            'name' => 'App\\Models\\HubtelPayment',
            'active' => true,
            'collect' => true,
            'disburse' => true,
            'directDebit' => true
        ];
        $response = $this->post('api/processor', $requestData);
        $response->assertStatus(201);
        $response->assertJson(['success' => true]);
    }
}
