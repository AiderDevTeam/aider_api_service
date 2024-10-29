<?php

namespace Database\Seeders;

use App\Models\Processor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProcessorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Processor::query()->updateOrCreate(['name'=>'App\\Models\\HubtelPayment'],
        [
            'active'=> true,
            'collect'=>true,
            'disburse' =>true,
        ]);
    }
}
