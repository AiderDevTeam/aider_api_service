<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::updateOrCreate(['name' => 'modify account'], ['guard_name' => 'admin']);
        Permission::updateOrCreate(['name' => 'assign role'], ['guard_name' => 'admin']);
        Permission::updateOrCreate(['name' => 'revoke role'], ['guard_name' => 'admin']);
        Permission::updateOrCreate(['name' => 'assign permission'], ['guard_name' => 'admin']);
        Permission::updateOrCreate(['name' => 'revoke permission'], ['guard_name' => 'admin']);
    }
}
