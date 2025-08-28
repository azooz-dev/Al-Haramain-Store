<?php

namespace Database\Seeders\Review;

use App\Models\Review\Review;
use App\Models\User\User;
use App\Models\Product\Product;
use App\Models\Order\Order;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Nette\Utils\Random;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Review::create([
            'user_id' => User::first()->id,
            'product_id' => Product::first()->id,
            'order_id' => Order::first()->id,
            'rating' => 5,
            'status' => Random::element([Review::PENDING, Review::APPROVED, Review::REJECTED]),
        ]);

        Review::create([
            'user_id' => User::first()->id,
            'product_id' => Product::first()->id,
            'order_id' => Order::find(2)->id,
            'rating' => 5,
            'status' => Random::element([Review::PENDING, Review::APPROVED, Review::REJECTED]),
        ]);
    }
}
