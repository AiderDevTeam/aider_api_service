<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category1= Category::query()->updateOrCreate(['name' => 'Fashion & Accessories'],['name' => 'Fashion & Accessories']);
        $category2 = Category::query()->updateOrCreate(['name' => 'Electronics'], ['name' => 'Electronics']);
        $category3 = Category::query()->updateOrCreate(['name' => 'Phones, Tablets & Accessories'], ['name' => 'Phones, Tablets & Accessories']);
        $category4 =  Category::query()->updateOrCreate(['name' => 'Beauty & Cosmetics'], ['name' => 'Beauty & Cosmetics']);
        $category5 = Category::query()->updateOrCreate(['name' => 'Baby & Mother Care'], ['name' => 'Baby & Mother Care']);

        //Fashion & Accessories
        SubCategory::query()->updateOrCreate(['name' => 'Sneakers'],['name' => 'Sneakers', 'category_id' => $category1->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Slippers & Sandals'],['name' => 'Slippers & Sandals', 'category_id' => $category1->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Boots'],['name' => 'Boots', 'category_id' => $category1->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Loafers'],['name' => 'Loafers', 'category_id' => $category1->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Heels'],['name' => 'Heels', 'category_id' => $category1->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Shoes'],['name' => 'Shoes', 'category_id' => $category1->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Shoulder Bags'],['name' => 'Shoulder Bags', 'category_id' => $category1->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Backpacks'],['name' => 'Backpacks', 'category_id' => $category1->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Shoes'],['name' => 'Shoes', 'category_id' => $category1->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Luggage & Traveling Bags'],['name' => 'Luggage & Traveling Bags', 'category_id' => $category1->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Hand Bags'],['name' => 'Hand Bags', 'category_id' => $category1->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Wallets & Purses'],['name' => 'Wallets & Purses', 'category_id' => $category1->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Dresses'],['name' => 'Dresses', 'category_id' => $category1->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Fabrics'],['name' => 'Fabrics', 'category_id' => $category1->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Shirts'],['name' => 'Shirts', 'category_id' => $category1->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'T-Shirts'],['name' => 'T-Shirts', 'category_id' => $category1->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Suits & Ties'],['name' => 'Suits & Ties', 'category_id' => $category1->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Sports Wear'],['name' => 'Sports Wear', 'category_id' => $category1->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Blouses'],['name' => 'Blouses', 'category_id' => $category1->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Skirts'],['name' => 'Skirts', 'category_id' => $category1->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Sweatshirts & Hoodies'],['name' => 'Sweatshirts & Hoodies', 'category_id' => $category1->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Jeans & Cargo Pants'],['name' => 'Jeans & Cargo Pants', 'category_id' => $category1->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Jackets & Coats'],['name' => 'Jackets & Coats', 'category_id' => $category1->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Shorts & Trousers'],['name' => 'Shorts & Trousers', 'category_id' => $category1->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Joggers & Sweatpants'],['name' => 'Joggers & Sweatpants', 'category_id' => $category1->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Belts'],['name' => 'Belts', 'category_id' => $category1->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Hats & Caps'],['name' => 'Hats & Caps', 'category_id' => $category1->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Sunglasses'],['name' => 'Sunglasses', 'category_id' => $category1->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Socks'],['name' => 'Socks', 'category_id' => $category1->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Underwear'],['name' => 'Underwear', 'category_id' => $category1->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Watches & Jewelleries'],['name' => 'Watches & Jewelleries', 'category_id' => $category1->id, 'external_id' => uniqid()]);

        //Phones, Tablets & Accessories
        SubCategory::query()->updateOrCreate(['name' => 'Smart watch'],['name' => 'Smart watch', 'category_id' => $category3->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Batteries'],['name' => 'Batteries', 'category_id' => $category3->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Cases & Screen Protectors'],['name' => 'Cases & Screen Protectors', 'category_id' => $category3->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Chargers & Power Adaptors'],['name' => 'Chargers & Power Adaptors', 'category_id' => $category3->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Phone Screens'],['name' => 'Phone Screens', 'category_id' => $category3->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Power Bank'],['name' => 'Power Bank', 'category_id' => $category3->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Headphones & Airpods'],['name' => 'Headphones & Airpods', 'category_id' => $category3->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Smart Phone'],['name' => 'Smart Phone', 'category_id' => $category3->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Yam'],['name' => 'Yam', 'category_id' => $category3->id, 'external_id' => uniqid()]);

        //Electronics
        SubCategory::query()->updateOrCreate(['name' => 'Tvs'],['name' => 'Tvs', 'category_id' => $category2->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Blender'],['name' => 'Blender', 'category_id' => $category2->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Cookers'],['name' => 'Cookers', 'category_id' => $category2->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Microwaves'],['name' => 'Microwaves', 'category_id' => $category2->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Fans'],['name' => 'Fans', 'category_id' => $category2->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Irons'],['name' => 'Irons', 'category_id' => $category2->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Water Heaters'],['name' => 'Water Heaters', 'category_id' => $category2->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Toasters'],['name' => 'Toasters', 'category_id' => $category2->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Home Theatre'],['name' => 'Home Theatre', 'category_id' => $category2->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Bluetooth Speakers'],['name' => 'Bluetooth Speakers', 'category_id' => $category2->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Headphones'],['name' => 'Headphones', 'category_id' => $category2->id, 'external_id' => uniqid()]);
        SubCategory::query()->updateOrCreate(['name' => 'Computer Accessories'],['name' => 'Computer Accessories', 'category_id' => $category2->id, 'external_id' => uniqid()]);

    }
}
