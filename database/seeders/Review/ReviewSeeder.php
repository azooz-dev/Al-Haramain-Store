<?php

namespace Database\Seeders\Review;

use App\Models\Review\Review;
use App\Models\User\User;
use App\Models\Product\Product;
use App\Models\Order\Order;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some sample reviews using factories
        Review::factory(20)->create();

        // Create specific reviews for testing
        Review::create([
            'user_id' => User::first()->id,
            'product_id' => Product::first()->id,
            'order_id' => Order::first()->id,
            'rating' => 5,
            'comment' => 'The product is very awesome',
            'locale' => 'en',
            'status' => collect([Review::PENDING, Review::APPROVED, Review::REJECTED])->random(),
        ]);

        Review::create([
            'user_id' => User::first()->id,
            'product_id' => Product::first()->id,
            'order_id' => Order::find(2)->id,
            'rating' => 5,
            'comment' => 'المنتج مرره رهييب',
            'locale' => 'ar',
            'status' => collect([Review::PENDING, Review::APPROVED, Review::REJECTED])->random(),
        ]);
    }
}
