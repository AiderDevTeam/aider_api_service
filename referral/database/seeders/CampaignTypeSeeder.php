<?php

namespace Database\Seeders;

use App\Models\CampaignType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CampaignTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'User Referral',
                'description' => 'Type created for users to refer other people to the platform',
                'short_code' => 'user_referral'
            ],
            [
                'name' => 'Marketing Referral',
                'description' => 'Created by Marketing to refer users to the platform',
                'short_code' => 'marketing_referral'
            ],
            [
                'name' => 'Sales Agent Referral',
                'description' => 'Type created for users tagged as ambassadors to refer people to the platform',
                'short_code' => 'sales_agent_referral'
            ]
        ];


        foreach($types as $type){
            CampaignType::updateOrCreate(
                [
                    'short_code' => $type['short_code']
                ], $type
            );
        }
    }
}
