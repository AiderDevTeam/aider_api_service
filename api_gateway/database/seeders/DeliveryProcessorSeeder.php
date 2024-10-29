<?php

namespace Database\Seeders;

use App\Models\DeliveryProcessor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeliveryProcessorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DeliveryProcessor::query()->updateOrCreate(
            ['name' => 'App\Models\WegooDelivery'], [
            'external_id' => uniqid('DP'),
            'active' => true,
            'express' => true,
            'next_day' => true
        ]);
    }
}
