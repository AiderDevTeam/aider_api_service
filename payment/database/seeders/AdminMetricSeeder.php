<?php

namespace Database\Seeders;

use App\Models\AdminMetric;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminMetricSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AdminMetric::create([
            'service' => 'payment'
        ]);
    }
}
