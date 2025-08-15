<?php

namespace Database\Seeders\Offer;

use App\Models\Offer\Offer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Offer::create([
            'name' => 'Offer 1',
            'description' => 'This is a test offer',
            'image_path' => 'https://images.unsplash.com/photo-1607748862156-7c548e7e98f4?w=800&h=600&fit=crop',
            'discount_type' => Offer::FIXED,
            'discount_amount' => 100,
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'status' => Offer::ACTIVE,
        ]);
    }
}
