<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Admin::query()->updateOrCreate([
            'email' => 'sk@itspoynt.com',
        ], [
            'first_name' => 'Kweku',
            'last_name' => 'Hammond',
            'gender' => 'male',
            'phone' => '0501376828',
            'birthday' => now()->subYears(30)->toDateTimeString(),
            'email_verified_at' => now()->toDateTimeString(),
            'password' => 'password',
        ]);
        $admin->assignRole(Admin::ROLES['ADMIN']);
    }
}
