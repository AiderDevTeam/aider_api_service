<?php

namespace Database\Seeders;

use App\Models\VASPayment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VASPaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        VASPayment::create([
            'external_id' => '64ca39b12f9c6',
            'user_id' => 2,
            'description' => 'DATANVDR1DLY',
            'created_at' => '2023-08-02 11:10:41',
            'updated_at' => '2023-08-02 11:10:41',
            'value' => 2.0,
            'type' => VASPayment::DATA_BUNDLE_PURCHASE,
        ]);
    }
}
