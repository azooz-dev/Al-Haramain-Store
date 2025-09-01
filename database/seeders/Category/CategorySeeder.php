<?php

namespace Database\Seeders\Category;

use App\Models\Category\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample categories using factory
        Category::factory(20)->create();

        // Create specific category for testing
        Category::create([
            'slug' => Str::slug('electronics'),
            'image' => 'image.jpg',
        ]);
    }
}
