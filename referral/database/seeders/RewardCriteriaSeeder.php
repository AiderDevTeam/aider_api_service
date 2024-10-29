<?php

namespace Database\Seeders;

use App\Models\RewardCriteria;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RewardCriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $criteria = [
            [
                'criteria' => 'Complete KYC',
                'short_code'=> 'complete_kyc'
            ],
            [
                'criteria' => 'Earn a point',
                'short_code'=> 'earn_point'
            ],
            [
                'criteria' => 'Make first Purchase',
                'short_code' => 'first_purchase'
            ],
            [
                'criteria' => 'Complete a transaction',
                'short_code' => 'complete_transaction'
            ]
        ];
        array_map(function($crit){
            RewardCriteria::updateOrCreate([
                'short_code' => $crit['short_code']
            ],$crit);
        }, $criteria);
    }
}
