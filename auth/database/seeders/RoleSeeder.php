<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::query()->updateOrCreate([
            'name' => Admin::ROLES['ADMIN']],
            ['guard_name' => 'admin'
            ]);

        Role::query()->updateOrCreate([
            'name' => Admin::ROLES['FINANCE']],
            ['guard_name' => 'admin'
            ]);

        Role::query()->updateOrCreate([
            'name' => Admin::ROLES['CUSTOMER_SERVICE']],
            ['guard_name' => 'admin'
            ]);

        Role::query()->updateOrCreate([
            'name' => Admin::ROLES['MARKETING']],
            ['guard_name' => 'admin'
            ]);
    }
}
