<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListDataBundlesTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_data_bundles_available(): void
    {
        $requestData = [
            'rSwitch' => 'MTN',
            'accountNumber' => '0542063963'
        ];

        $response = $this->get(route('dataBundles.route', $requestData));
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }
}
