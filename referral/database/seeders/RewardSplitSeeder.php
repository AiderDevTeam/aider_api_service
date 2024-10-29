<?php

namespace Database\Seeders;

use App\Models\RewardSplit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RewardSplitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $splits = [
            [
                'split_type' => 'Referrer to Enjoy'
            ],
            [
                'split_type' => 'Referred to Enjoy'
            ],
            [
                'split_type' => 'Both to Enjoy'
            ]
        ];

        foreach($splits as $key => $split){
            $key = $key + 1;
            RewardSplit::updateOrCreate([
                'id' => $key
            ],$split);
        }
    }

    
}
