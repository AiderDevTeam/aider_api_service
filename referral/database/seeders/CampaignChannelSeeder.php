<?php

namespace Database\Seeders;

use App\Models\CampaignChannel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CampaignChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $channels = [
            [
                'channel' => 'YouTube',
                'availability' => 'Available to Marketing Referral'
            ],
            [
                'channel' => 'Instagram',
                'availability' => 'Available to Marketing Referral'
            ],
            [
                'channel' => 'Facebook',
                'availability' => 'Available to Marketing Referral'
            ],
            [
                'channel' => 'Twitter',
                'availability' => 'Available to Marketing Referral'
            ],
            [
                'channel' => 'Telegram',
                'availability' => 'Available to Marketing Referral'
            ],
            [
                'channel' => 'LinkedIn',
                'availability' => 'Available to Marketing Referral'
            ],
            [
                'channel' => 'Influencer',
                'availability' => 'Available to Marketing Referral'
            ],
            [
                'channel' => 'SMS',
                'availability' => 'Available to Marketing Referral'
            ],
            [
                'channel' => 'Event',
                'availability' => 'Available to Marketing Referral'
            ],
            [
                'channel' => 'Customer Service',
                'availability' => 'Available to Marketing Referral'
            ],
            [
                'channel' => 'TV',
                'availability' => 'Available to Marketing Referral'
            ],
            [
                'channel' => 'Radio',
                'availability' => 'Available to Marketing Referral'
            ],
            [
                'channel' => 'Newsletter',
                'availability' => 'Available to Marketing Referral'
            ],
            [
                'channel' => 'Poynt Ambassadors ',
                'availability' => 'Available to Sales Agent Referral'
            ],
            [
                'channel' => 'User Referral',
                'availability' => 'Available to Sales Agent Referral'
            ]
        ];

        array_map(function($channel){
            $save = CampaignChannel::updateOrCreate([
                'channel' => $channel['channel']
            ],$channel);
        }, $channels);
    }
}
