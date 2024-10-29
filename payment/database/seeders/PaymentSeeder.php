<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\VASPayment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Payment::query()->create([
            'external_id' => '64ca39b130081',
            'paymentable_type' => 'App\Models\VASPayment',
            'paymentable_id' => 19,
            'amount' => 1.97,
            'user_id' => 2,
            'type' => VASPayment::DATA_BUNDLE_PURCHASE,
            'destination_account_name' => 'Joseph Nii Attram Mensah',
            'destination_account_number' => '0207932004',
            'destination_sort_code' => 'VOD',
            'disbursement_status' => 'pending',
            'collection_sort_code' => 'VOD',
            'collection_account_name' => 'Joseph Nii Attram Mensah',
            'collection_account_number' => '0207932004',
            'collection_status' => 'pending',
            'disbursement_tries' => 0,
            'reversal_tries' => 0,
            'created_at' => '2023-08-02 11:10:41',
            'updated_at' => '2023-08-02 11:10:41',
        ]);
    }
}
