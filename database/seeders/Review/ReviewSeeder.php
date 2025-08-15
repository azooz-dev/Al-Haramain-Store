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
            'user_id' => User::random()->id,
            'product_id' => Product::random()->id,
            'order_id' => Order::random()->id,
            'rating' => 5,
            'comment' => 'This is a test review',
            'status' => Random::element([Review::PENDING, Review::APPROVED, Review::REJECTED]),
        ]);
    }
}
