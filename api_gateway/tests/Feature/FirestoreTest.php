<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FirestoreTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_firestore_data_upload(): void
    {
        $requestPayload = [
            'externalId' => '000123456789',
            'collection' => 'local_transactions',
            'data' => ["test"]
        ];
        $response = $this->post('api/firestore', $requestPayload);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }
}
