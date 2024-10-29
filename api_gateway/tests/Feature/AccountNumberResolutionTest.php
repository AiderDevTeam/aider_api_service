<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AccountNumberResolutionTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_account_name_resolution(): void
    {
        $accountNUmber = '0501376828';
        $bankCode = 'VOD';

        $response = $this->get('api/accounts/resolve?accountNumber='.$accountNUmber.'&bankCode='.$bankCode);
        $response->assertStatus(200);
        $response->assertJson(['success'=> true]);
    }
}
