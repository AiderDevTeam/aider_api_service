<?php

namespace Database\Seeders;

use App\Models\DeliveryFee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeliveryFeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DeliveryFee::query()->create([
            'external_id' => uniqid('DF'),
            'processor' => 'wegoo',
            'delivery_option' => 'SAME_DAY',
            'fee' => 30,
            'margin' => 5
        ]);
    }
}
