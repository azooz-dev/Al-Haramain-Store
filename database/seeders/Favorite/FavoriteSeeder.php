<?php

namespace Database\Seeders\Favorite;

use App\Models\Favorite\Favorite;
use App\Models\Product\Product;
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
        Favorite::create([
            'user_id' => User::random()->id,
            'product_id' => Product::random()->id,
        ]);
    }
}
