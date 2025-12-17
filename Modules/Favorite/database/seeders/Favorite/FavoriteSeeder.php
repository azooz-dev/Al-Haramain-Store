<?php

namespace Modules\Favorite\Database\Seeders\Favorite;

use Modules\Favorite\Entities\Favorite\Favorite;
use Modules\Catalog\Entities\Product\Product;
use Modules\Catalog\Entities\Product\ProductColor;
use Modules\Catalog\Entities\Product\ProductVariant;
use Modules\User\Entities\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample favorites using factory
        Favorite::factory(100)->create();

        // Create specific favorite for testing
        Favorite::create([
            'user_id' => User::first()->id,
            'product_id' => Product::first()->id,
            'color_id' => ProductColor::first()->id,
            'variant_id' => ProductVariant::first()->id,
        ]);
    }
}
