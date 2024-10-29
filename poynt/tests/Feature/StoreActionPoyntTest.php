<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StoreActionPoyntTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_action_poynt(): void
    {
        $response = $this->post('api/admin/action-poynt', [
            'action' => 'airtime purchase',
            'poynt' => 10,
            'type' => 'value'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success'=>true]);
        $this->assertDatabaseHas('action_poynts', [
            'action' => 'airtime purchase',
            'poynt' => 10,
            'type' => 'value'
        ]);
    }
}
