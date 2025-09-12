<?php

namespace Database\Seeders\Favorite;

use App\Models\Favorite\Favorite;
use App\Models\Product\Product;
use App\Models\Product\ProductColor;
use App\Models\Product\ProductVariant;
use App\Models\User\User;
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
