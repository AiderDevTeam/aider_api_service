<?php

namespace Database\Seeders;

use App\Models\RewardType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RewardTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'type' => 'Cash'
            ],
            [
                'type' => 'Points'
            ]
        ];

     foreach($types as $key => $type){
        RewardType::updateOrCreate([
            'id' => $key + 1
        ],$type);
     }
            

    }
}
