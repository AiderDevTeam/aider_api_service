<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetActionPoyntsTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_get_action_poynts(): void
    {
        $response = $this->get('/api/admin/action-poynt');
        $response->assertStatus(200);
        $response->assertJson(['success'=>true]);
    }
}
