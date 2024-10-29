<?php

namespace Database\Seeders;

use App\Models\Transaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run()
    {
        Transaction::query()->create([
            'user_id' => 2,
            'payment_id' => 16,
            'external_id' => '64ca39bb51424',
            'amount' => 1.97,
            'account_number' => '0207932004',
            'status' => 'started',
            'r_switch' => 'VOD',
            'created_at' => '2023-08-02 11:10:51',
            'updated_at' => '2023-08-02 11:10:51',
        ]);
    }
}
