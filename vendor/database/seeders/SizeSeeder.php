<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Size::query()->create(['name' => 'Tvs', 'external_id' => uniqid(), 'size_value' => '32"']);
        Size::query()->create(['name' => 'Tvs', 'external_id' => uniqid(), 'size_value' => '40"']);
        Size::query()->create(['name' => 'Tvs', 'external_id' => uniqid(), 'size_value' => '43"']);
        Size::query()->create(['name' => 'Tvs', 'external_id' => uniqid(), 'size_value' => '50"']);
        Size::query()->create(['name' => 'Tvs', 'external_id' => uniqid(), 'size_value' => '55"']);
        Size::query()->create(['name' => 'Tvs', 'external_id' => uniqid(), 'size_value' => '65"']);
        Size::query()->create(['name' => 'Tvs', 'external_id' => uniqid(), 'size_value' => '75"']);

        Size::query()->create(['name' => 'Ladies', 'external_id' => uniqid(), 'size_value' => 'UK 4']);
        Size::query()->create(['name' => 'Ladies', 'external_id' => uniqid(), 'size_value' => 'UK 6']);
        Size::query()->create(['name' => 'Ladies', 'external_id' => uniqid(), 'size_value' => 'UK 8']);
        Size::query()->create(['name' => 'Ladies', 'external_id' => uniqid(), 'size_value' => 'UK 10']);
        Size::query()->create(['name' => 'Ladies', 'external_id' => uniqid(), 'size_value' => 'UK 12']);
        Size::query()->create(['name' => 'Ladies', 'external_id' => uniqid(), 'size_value' => 'UK 14']);
        Size::query()->create(['name' => 'Ladies', 'external_id' => uniqid(), 'size_value' => 'UK 16']);
        Size::query()->create(['name' => 'Ladies', 'external_id' => uniqid(), 'size_value' => 'UK 18']);
        Size::query()->create(['name' => 'Ladies', 'external_id' => uniqid(), 'size_value' => 'UK 20']);
        Size::query()->create(['name' => 'Ladies', 'external_id' => uniqid(), 'size_value' => 'UK 22']);
        Size::query()->create(['name' => 'Ladies', 'external_id' => uniqid(), 'size_value' => 'UK 24']);

        Size::query()->create(['name' => 'General', 'external_id' => uniqid(), 'size_value' => 'XS']);
        Size::query()->create(['name' => 'General', 'external_id' => uniqid(), 'size_value' => 'S']);
        Size::query()->create(['name' => 'General', 'external_id' => uniqid(), 'size_value' => 'M']);
        Size::query()->create(['name' => 'General', 'external_id' => uniqid(), 'size_value' => 'l']);
        Size::query()->create(['name' => 'General', 'external_id' => uniqid(), 'size_value' => 'XL']);
        Size::query()->create(['name' => 'General', 'external_id' => uniqid(), 'size_value' => 'XXL']);
        Size::query()->create(['name' => 'General', 'external_id' => uniqid(), 'size_value' => 'XXXL']);

        Size::query()->create(['name' => 'Shoes', 'external_id' => uniqid(), 'size_value' => 'EU 36']);
        Size::query()->create(['name' => 'Shoes', 'external_id' => uniqid(), 'size_value' => 'EU 38']);
        Size::query()->create(['name' => 'Shoes', 'external_id' => uniqid(), 'size_value' => 'EU 39']);
        Size::query()->create(['name' => 'Shoes', 'external_id' => uniqid(), 'size_value' => 'EU 40']);
        Size::query()->create(['name' => 'Shoes', 'external_id' => uniqid(), 'size_value' => 'EU 41']);
        Size::query()->create(['name' => 'Shoes', 'external_id' => uniqid(), 'size_value' => 'EU 42']);
        Size::query()->create(['name' => 'Shoes', 'external_id' => uniqid(), 'size_value' => 'EU 43']);
        Size::query()->create(['name' => 'Shoes', 'external_id' => uniqid(), 'size_value' => 'EU 44']);
        Size::query()->create(['name' => 'Shoes', 'external_id' => uniqid(), 'size_value' => 'EU 45']);
        Size::query()->create(['name' => 'Shoes', 'external_id' => uniqid(), 'size_value' => 'EU 46']);

        Size::query()->create(['name' => 'Fabric', 'external_id' => uniqid(), 'size_value' => '1 Yard']);
        Size::query()->create(['name' => 'Fabric', 'external_id' => uniqid(), 'size_value' => '2 Yard']);
        Size::query()->create(['name' => 'Fabric', 'external_id' => uniqid(), 'size_value' => '3 Yard']);
        Size::query()->create(['name' => 'Fabric', 'external_id' => uniqid(), 'size_value' => '4 Yards']);
        Size::query()->create(['name' => 'Fabric', 'external_id' => uniqid(), 'size_value' => '5 Yards']);
        Size::query()->create(['name' => 'Fabric', 'external_id' => uniqid(), 'size_value' => '6 Yards']);
        Size::query()->create(['name' => 'Fabric', 'external_id' => uniqid(), 'size_value' => '7 Yards']);
        Size::query()->create(['name' => 'Fabric', 'external_id' => uniqid(), 'size_value' => '8 Yards']);
        Size::query()->create(['name' => 'Fabric', 'external_id' => uniqid(), 'size_value' => '9 Yards']);
        Size::query()->create(['name' => 'Fabric', 'external_id' => uniqid(), 'size_value' => '10 Yards']);
        Size::query()->create(['name' => 'Fabric', 'external_id' => uniqid(), 'size_value' => '11 Yards']);
        Size::query()->create(['name' => 'Fabric', 'external_id' => uniqid(), 'size_value' => '12 Yards']);

        Size::query()->create(['name' => 'Pants', 'external_id' => uniqid(), 'size_value' => '30']);
        Size::query()->create(['name' => 'Pants', 'external_id' => uniqid(), 'size_value' => '31']);
        Size::query()->create(['name' => 'Pants', 'external_id' => uniqid(), 'size_value' => '32']);
        Size::query()->create(['name' => 'Pants', 'external_id' => uniqid(), 'size_value' => '33']);
        Size::query()->create(['name' => 'Pants', 'external_id' => uniqid(), 'size_value' => '34']);
        Size::query()->create(['name' => 'Pants', 'external_id' => uniqid(), 'size_value' => '35']);
        Size::query()->create(['name' => 'Pants', 'external_id' => uniqid(), 'size_value' => '36']);
        Size::query()->create(['name' => 'Pants', 'external_id' => uniqid(), 'size_value' => '37']);
        Size::query()->create(['name' => 'Pants', 'external_id' => uniqid(), 'size_value' => '38']);
        Size::query()->create(['name' => 'Pants', 'external_id' => uniqid(), 'size_value' => '39']);
        Size::query()->create(['name' => 'Pants', 'external_id' => uniqid(), 'size_value' => '40']);
    }
}
