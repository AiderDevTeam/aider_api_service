<?php

namespace Database\Seeders;

use App\Models\Size;
use App\Models\SubCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SizeSubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //tvs
        $tvSizes = Size::where('name','Tvs')->pluck('id');

        $tv = SubCategory::where('name','Tvs')->first();
        $tv->sizes()->sync($tvSizes);

        //ladies sizes
        $ladies = Size::where('name','Ladies')->pluck('id');

        $dresses = SubCategory::where('name','Dresses')->first();
        $dresses->sizes()->sync($ladies);

        $blouses = SubCategory::where('name','Blouses')->first();
        $blouses->sizes()->sync($ladies);

        $skirts = SubCategory::where('name','Skirts')->first();
        $skirts->sizes()->sync($ladies);

        //general size
        $general = Size::where('name','General')->pluck('id');

        $shirts = SubCategory::where('name','Shirts')->first();
        $shirts->sizes()->sync($general);

        $tShirts = SubCategory::where('name','T-Shirts')->first();
        $tShirts->sizes()->sync($general);

        $sweatshirts = SubCategory::where('name','Sweatshirts & Hoodies')->first();
        $sweatshirts->sizes()->sync($general);

        $jacket = SubCategory::where('name','Jackets & Coats')->first();
        $jacket->sizes()->sync($general);

        $sportswear = SubCategory::where('name','Sports Wear')->first();
        $sportswear->sizes()->sync($general);

        $joggers = SubCategory::where('name','Joggers & Sweatpants')->first();
        $joggers->sizes()->sync($general);

        $underwear = SubCategory::where('name','Underwear')->first();
        $underwear->sizes()->sync($general);

        //shoes
        $shoes = Size::where('name','Shoes')->pluck('id');

        $sneakers = SubCategory::where('name','Sneakers')->first();
        $sneakers->sizes()->sync($shoes);

        // $slippers = SubCategory::where('name','Slippers & Sandles')->first();
        // $slippers->sizes()->sync($shoes);

        $boots = SubCategory::where('name','Boots')->first();
        $boots->sizes()->sync($shoes);

        $loafers = SubCategory::where('name','Loafers')->first();
        $loafers->sizes()->sync($shoes);

        $heels = SubCategory::where('name','Heels')->first();
        $heels->sizes()->sync($shoes);

        $shoesSubCategory = SubCategory::where('name','Shoes')->first();
        $shoesSubCategory->sizes()->sync($shoes);

        //fabric
        $fabric = Size::where('name','Fabric')->pluck('id');

        $fabrics = SubCategory::where('name','Fabrics')->first();
        $fabrics->sizes()->sync($fabric);

        //Pants
        $pants = Size::where('name','Pants')->pluck('id');

        $jeans = SubCategory::where('name','Jeans & Cargo Pants')->first();
        $jeans->sizes()->sync($pants);

        $shorts = SubCategory::where('name','Shorts & Trousers')->first();
        $shorts->sizes()->sync($pants);
    }
}
