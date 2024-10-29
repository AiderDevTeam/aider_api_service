<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()
            ->updateOrCreate([
                'email' => 'karece@itspotynt.com'
            ], [
                'first_name' => 'El-Karece',
                'last_name' => 'Amoakoa',
                // 'username' => 'karece',
                'email' => 'karece@itspotynt.com',
                'birthday' => '',
                'gender' => 'female',
                'phone' => '0200327946',
                'calling_code' => '+233',
                'profile_photo_url' => '',
                'push_notification_token' => '',
                'referral_code' => '001',
                'referral_url' => 'karece001@itspoynts',
                'device_os' => 'ios',
                'terms_and_conditions' => true,
                'password' => 'password'
            ]);

        User::query()
            ->updateOrCreate([
                'email' => 'joeyberri@gmail.com'
            ], [
                'first_name' => 'Joseph',
                'last_name' => 'Mensah',
                // 'username' => 'joey',
                'email' => 'joeyberri@gmail.com',
                'birthday' => '',
                'gender' => 'male',
                'phone' => '0207932004',
                'calling_code' => '+233',
                'profile_photo_url' => '',
                'push_notification_token' => '',
                'referral_code' => '002',
                'referral_url' => 'joey002@itspoynts',
                'device_os' => 'android',
                'terms_and_conditions' => true,
                'password' => 'password'
            ]);

        $kweku = User::query()
            ->updateOrCreate([
                'email' => 'hammondkweku@gmail.com'
            ], [
                'first_name' => 'Kweku',
                'last_name' => 'Hammond',
                // 'username' => 'kweku',
                'email' => 'hammondkweku@gmail.com',
                'birthday' => '',
                'gender' => 'male',
                'phone' => '0501376828',
                'calling_code' => '+233',
                'profile_photo_url' => '',
                'push_notification_token' => '',
                'referral_code' => '003',
                'referral_url' => 'kweku003@itspoynts',
                'device_os' => 'android',
                'terms_and_conditions' => true,
                'password' => 'password'
            ]);

            $emo = User::query()
            ->updateOrCreate([
                'email' => 'emodatt08@gmail.com'
            ], [
                'first_name' => 'King',
                'last_name' => 'Emo',
                // 'username' => 'Emo',
                'email' => 'emodatt08@gmail.com',
                'birthday' => '',
                'gender' => 'male',
                'phone' => '0271763214',
                'calling_code' => '+233',
                'profile_photo_url' => '',
                'push_notification_token' => '',
                'referral_code' => '005',
                'referral_url' => 'emodatt08@itspoynts',
                'device_os' => 'ios',
                'terms_and_conditions' => true,
                'password' => 'password'
            ]);

            //save user type as user and ambassador
            $kweku->userTypes()->syncWithoutDetaching([1,3]); 
            //save user type as user
            $emo->userTypes()->syncWithoutDetaching([1]); 
    }
}
